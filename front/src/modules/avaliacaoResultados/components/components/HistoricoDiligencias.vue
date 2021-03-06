<template>
    <v-dialog
        v-model="dialog"
        transition="dialog-bottom-transition"
        scrollable
        fullscreen
    >
        <v-tooltip
            slot="activator"
            bottom>
            <v-btn
                slot="activator"
                flat
                icon
                @click.native="obterDiligencias(obj.idPronac)">
                <v-icon
                    :color="statusDiligencia(obj).color"
                    :change="statusDiligencia(obj).color"
                    class="material-icons">assignment_late</v-icon>
            </v-btn>
            <span>{{ statusDiligencia(obj).desc }} </span>
        </v-tooltip>

        <v-card v-if="Object.keys(diligencias).length > 0">

            <v-toolbar
                dark
                color="#0a420e !important">
                <v-btn
                    icon
                    dark
                    @click="dialog = false">
                    <v-icon>close</v-icon>
                </v-btn>
                <v-toolbar-title>Diligências Projeto: {{ info.pronac }} - {{ info.nomeProjeto }} </v-toolbar-title>
            </v-toolbar>

            <template>
                <div>
                    <v-data-table
                        v-if="Object.keys(diligencias).length > 0"
                        :headers="headers"
                        :items="sortByDate"
                        item-key="idDiligencia"
                        class="elevation-1"
                        rows-per-page-text="Items por Página"
                        no-data-text="Nenhum dado encontrado"
                    >
                        <template
                            slot="items"
                            slot-scope="props">
                            <tr
                                class="line"
                                @click="props.expanded = !props.expanded; arrow = !arrow">
                                <td
                                    v-if="props.item.produto"
                                    class="text-xs-left">
                                    {{ props.item.produto }}
                                </td>
                                <td
                                    v-else
                                    class="text-xs-left"> - </td>
                                <td class="text-xs-left">{{ props.item.tipoDiligencia }}</td>
                                <td class="text-xs-center">{{ props.item.dataSolicitacao | date }}</td>
                                <td
                                    v-if="props.item.dataResposta === null "
                                    class="text-xs-center">{{ props.item.dataResposta }}</td>
                                <td
                                    v-if="props.item.dataResposta !== null "
                                    class="text-xs-center">{{ props.item.dataResposta | date }}</td>
                                <td class="text-xs-center">{{ statusDiligencia(props.item).prazo }}</td>
                                <td class="text-xs-left">Prorrogado</td>
                                <td>
                                    <v-icon v-if="arrow">keyboard_arrow_down</v-icon>
                                    <v-icon v-else>keyboard_arrow_up</v-icon>
                                </td>
                            </tr>
                        </template>
                        <template
                            slot="expand"
                            slot-scope="props">
                            <v-card flat>
                                <v-card-text v-if="Object.keys(diligencias).length > 0">
                                    <v-container fluid>
                                        <div v-if="props.item.Solicitacao">
                                            <v-layout
                                                justify-space-around
                                                row
                                                wrap>
                                                <v-flex
                                                    lg12
                                                    dark>
                                                    <b>SOLICITAÇÃO</b>
                                                </v-flex>
                                                <v-flex>
                                                    <p v-html="props.item.Solicitacao"/>
                                                </v-flex>
                                            </v-layout>
                                        </div>

                                        <div v-if="props.item.Resposta">
                                            <v-layout
                                                justify-space-around
                                                row
                                                wrap>
                                                <v-flex
                                                    lg12
                                                    dark>
                                                    <b>RESPOSTA</b>
                                                </v-flex>
                                                <v-flex>
                                                    <p v-html="props.item.Resposta"/>
                                                </v-flex>
                                            </v-layout>
                                        </div>

                                        <div v-if="props.item.Arquivos && Object.keys(props.item.Arquivos).length > 0">
                                            <v-flex
                                                lg12
                                                dark
                                                class="text-xs-center">
                                                <b>ARQUIVOS ANEXADOS</b>
                                            </v-flex>
                                            <v-container grid-list-md>
                                                <v-layout
                                                    justify-space-around
                                                    row
                                                    wrap>
                                                    <v-flex xs6>
                                                        <b>Arquivo</b>
                                                    </v-flex>
                                                    <v-flex xs2>
                                                        <b>Dt.Envio</b>
                                                    </v-flex>
                                                </v-layout>
                                                <v-layout
                                                    v-for="arquivo of props.item.Arquivos"
                                                    :key="arquivo.idArquivo"
                                                    justify-space-around
                                                    align-center
                                                    row
                                                >
                                                    <v-flex xs6>
                                                        <p>
                                                            <a
                                                                :href="`/upload/abrir?id=${arquivo.idArquivo}`"
                                                                target="_blank">
                                                                {{ arquivo.nmArquivo }}
                                                            </a>
                                                        </p>
                                                    </v-flex>
                                                    <v-flex xs2>
                                                        <p>
                                                            {{ arquivo.dtEnvio }}
                                                        </p>
                                                    </v-flex>
                                                </v-layout>
                                            </v-container>
                                        </div>
                                    </v-container>
                                </v-card-text>
                                <v-card-text v-else>
                                    <Carregando :text="'Carregando ...'"/>
                                </v-card-text>
                            </v-card>
                        </template>
                    </v-data-table>
                </div>
            </template>

        </v-card>
        <v-card-text v-else>
            <Carregando :text="'Carregando ...'"/>
        </v-card-text>
        <v-divider/>
    </v-dialog>

</template>

<script>
import Vue from 'vue';
import Carregando from '@/components/CarregandoVuetify';
import _ from 'lodash';
import { mapActions, mapGetters } from 'vuex';
import Data from '../../../../filters/date';

Vue.filter('date', Data);

export default {
    name: 'HistoricoDiligencias',
    components: {
        Carregando,
    },
    props: { obj: { type: Object, default: () => {} } },
    data() {
        return {
            dialog: false,
            show: {
                solicitacao: false,
                resposta: false,
                index: '',
            },
            info: {
                nomeProjeto: 'Nenhuma Diligência Registrada',
                pronac: '',
            },
            headers: [
                {
                    text: 'PRODUTO',
                    align: 'left',
                    value: 'produto',
                },
                {
                    text: 'TIPO DE DILIGÊNCIA',
                    align: 'left',
                    value: 'tipoDiligencia',
                },
                {
                    text: 'DATA DA SOLICITAÇÃO',
                    align: 'center',
                    value: 'dataSolicitacao',
                },
                {
                    text: 'DATA DA RESPOSTA',
                    align: 'center',
                    value: 'dataResposta',
                },
                {
                    text: 'PRAZO DA RESPOSTA',
                    align: 'center',
                    value: 'prazoResposta',
                },
                {
                    text: 'PRORROGADO',
                    value: 'prorrogado',
                    sortable: false,
                    align: 'left',
                },
                {
                    text: '',
                    sortable: false,
                },
            ],
            arrow: true,
        };
    },
    computed: {
        ...mapGetters({
            diligencias: 'avaliacaoResultados/diligenciasHistorico',
        }),

        sortByDate() {
            return _.orderBy(this.diligencias.items, 'dataSolicitacao', 'desc');
        },
    },
    updated() {
        this.setInfo();
    },
    methods: {
        ...mapActions({
            obterDiligencias: 'avaliacaoResultados/obetDadosDiligencias',
        }),
        mostrarSolicitacao(index) {
            this.show.solicitacao = !this.show.solicitacao;
            this.show.index = index;
        },
        mostrarResposta(index) {
            this.show.resposta = !this.show.resposta;
            this.show.index = index;
        },
        statusDiligencia(obj) {
            let fim = new Date();
            const prazo = this.prazoResposta(obj);

            const result = new Date(obj.dataSolicitacao);
            fim.setTime(result.getTime() + (40 * 24 * 60 * 60 * 1000));

            fim = fim.toLocaleString(['pt-BR'], {
                month: '2-digit',
                day: '2-digit',
                year: 'numeric',
            });

            let status = {
                color: 'grey',
                desc: 'Histórico Diligências',
                prazo: fim,
            };
            const prazoPadrao = 40;
            // diligenciado
            if (obj.DtSolicitacao && obj.DtResposta === ''
                && prazo <= prazoPadrao && obj.stEnviado === 'S') {
                status = { color: 'yellow', desc: 'Diligenciado', prazo: fim };
                return status;
                // diligencia não respondida
            } if (obj.DtSolicitacao && obj.DtResposta === '' && prazo > prazoPadrao) {
                status = { color: 'red', desc: 'Diligencia não respondida', prazo: fim };
                return status;
                // diligencia respondida com ressalvas
            } if (obj.DtSolicitacao && obj.DtResposta !== '') {
                if (obj.stEnviado === 'N' && prazo > prazoPadrao) {
                    status = { color: 'red', desc: 'Diligencia não respondida', prazo: fim };
                    return status;
                }
                if (obj.stEnviado === 'N' && prazo < prazoPadrao) {
                    status = { color: 'yellow', desc: 'Diligenciado', prazo: fim };
                    return status;
                }

                status = { color: 'blue', desc: 'Diligencia respondida', prazo: fim };
                return status;
            }
            status = { color: 'green', desc: 'A Diligenciar', prazo: fim };
            return status;
        },
        prazoResposta(obj) {
            /**
             If (notempty dtSolicitação){
             Calculo do Prazo

             prazo = date.now() - datainicial(dtSolicitacao);

              converter.dias(prazo)

             -> Para casos de de ser contagem regressiva.
             if (key boolean (bln_descrescente) ){
              prazo = prazoPadrao - prazo(do calculo acima);
             }

             if(prazo > 0) { prazo positivo
              return prazo
             } else if( prazo <= 0) { prazo negativo
                return 0
             } else {        para prazo de resposta igual ao padrão
              return -1
             }
             }else {
             return 0
             }
             */

            let now;
            let timeDiff;
            let prazo;
            if (typeof obj.dataSolicitacao !== 'undefined') {
                now = Date.now();
                timeDiff = Math.abs(now - new Date(obj.dataSolicitacao));
                prazo = Math.ceil(timeDiff / (1000 * 3600 * 24));
                // console.info(new Date().toLocaleDateString(undefined, {
                //     day: '2-digit',
                //     month: '2-digit',
                //     year: 'numeric'
                // }) + " - "+ new Date(obj.DtSolicitacao).toLocaleDateString(undefined, {
                //     day: '2-digit',
                //     month: '2-digit',
                //     year: 'numeric'
                // }) + " = "+ prazo);

                if (prazo > 0) {
                    // prazo positivo

                    return prazo;
                }
                if (prazo <= 0) {
                    // prazo negativo
                    return 0;
                }
                if (prazo === 40) {
                    // para prazo de resposta igual ao padrão
                    return -1;
                }
            }
            return null;
        },
        setInfo() {
            if (Object.keys(this.diligencias).length > 0) {
                this.info.nomeProjeto = this.diligencias.items[0].nomeProjeto;
                this.info.pronac = this.diligencias.items[0].pronac;
                return this.info;
            }
            return this.info;
        },
    },
};
</script>
<style scoped>
    .line {
        cursor: pointer;
    }
</style>
