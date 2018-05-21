Vue.component('readequacao-formulario', {
    template: `
    <div class="card">
        <div class="card-content">
            <span class="card-title">Solicita&ccedil;&atilde;o de readequa&ccedil;&atilde;o</span>
            <input type="hidden" v-model="readequacao.idReadequacao"/>
            <div class="row">
                <div class="input-field col s12">
                        <textarea
                            id="textarea1"
                            class="materialize-textarea"
                            ref="readequacaoJustificativa"
                            v-model="readequacao.justificativa"></textarea>
                    <label for="textarea1">Justificativa *</label>
                </div>
            </div>
            <component
                v-bind:is="componenteDsSolicitacao"
                :ds-solicitacao="readequacao.dsSolicitacao"
                v-on:eventoAtualizarDsSolicitacao="readequacao.dsSolicitacao=$event"
            ></component>
            <div class="row">
                <div class="col s12">
                    <span>Anexar arquivo</span>
                    <div class="file-field input-field">
                        <div class="btn">
                            <span>File</span>
                            <input type="file"
                                   name="arquivo"
                                   id="arquivo"
                                   @change="subirDocumento">
                        </div>
                        <div class="file-path-wrapper">
                            <input class="file-path validate" type="text">
                        </div>
                        <input type="hidden" v-model="readequacao.idDocumento"/>
                    </div>
                    <div id="carregando-arquivo" class="progress sumir">
                      <div class="indeterminate"></div>
                    </div>
                    <div class="col s12">
                        <a v-bind:href="'/upload/abrir?id=' + readequacao.idDocumento" v-if="readequacao.idDocumento !=''">
                            {{readequacao.nomeArquivo }}
                        </a>
                        <a
                            v-show="readequacao.idDocumento"
                            v-on:click="excluirDocumento"
                            title="Remover aquivo"
                            class=" small waves-effect waves-light red-text lighten-2">
                            <i class="material-icons">delete</i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="right-align padding20 col s12">
                    <button
                        v-on:click="salvarReadequacao"
                        class="waves-effect waves-light btn btn-primary">
                        <i class="material-icons right">save</i>Salvar
                    </button>
                </div>
            </div>
        </div>
    </div>
    `,
    data: function () {
        return {
            readequacao: {
                'idPronac': '',
                'idReadequacao': '',
                'justificativa': '',
                'arquivo': '',
                'idTipoReadequacao': '',
                'dsSolicitacao': '',
                'idDocumento': '',
                'nomeArquivo': ''
            },
            arquivo: {
                tamanhoMaximo: 500000,
                tiposAceitos: ['pdf']
            }
        }
    },
    props: {
        'idPronac': '',
        'idTipoReadequacao': '',
        'objReadequacao': {},
        'componenteDsSolicitacao': ''
    },
    mixins: [utils],
    mounted: function () {
        if (typeof this.objReadequacao == 'undefined') {
            this.obterDadosReadequacao();
        } else {
            this.readequacao = this.objReadequacao;
        }
    },
    watch: {
        objReadequacao: function (value) {
            this.readequacao = value;
        }
    },
    methods: {
        obterDadosReadequacao: function () {
            let self = this;
            $3.ajax({
                type: "GET",
                url: "/readequacao/readequacoes/obter-dados-readequacao",
                data: {
                    idTipoReadequacao: self.idTipoReadequacao,
                    idPronac: self.idPronac
                }
            }).done(function (response) {
                if (response.readequacao != null) {
                    self.readequacao = response.readequacao;
                }
            });
        },
        salvarReadequacao: function () {
            if (this.readequacao.justificativa.length == 0) {
                this.mensagemAlerta("\xC9 obrigat\xF3rio preencher a justificativa da readequa\xE7\xE3o!");
                this.$refs.readequacaoJustificativa.focus();
                return;
            }

            this.$emit('eventoAtualizarReadequacao', this.readequacao);
        },
        subirDocumento: function () {
            let arquivo = $('#arquivo')[0].files[0],
                self = this;

            if (!this.validarDocumento(arquivo)) {
                return;
            }
            var formData = new FormData();
            formData.append('arquivo', arquivo);
            formData.append('idPronac', self.idPronac);
            formData.append('idReadequacao', self.readequacao.idReadequacao);
            formData.append('idTipoReadequacao', self.idTipoReadequacao);
            formData.append('idDocumentoAtual', self.readequacao.idDocumento);

            $3('#carregando-arquivo').fadeIn('slow');
            $3.ajax(
                Object.assign(
                    {},
                    {
                        type: "POST",
                        url: "/readequacao/readequacoes/salvar-documento/idPronac/" + self.idPronac,
                        processData: false,
                        contentType: false,
                    },
                    {
                        data: formData,
                    }
                )
            ).done(function (response) {
                self.readequacao.idDocumento = response.documento.idDocumento;
                self.readequacao.nomeArquivo = response.documento.nomeArquivo;
                self.readequacao.idReadequacao = response.readequacao.idReadequacao;
                self.$emit('eventoAtualizarReadequacao', self.readequacao);
                $3('#carregando-arquivo').fadeOut('slow');
            });
        },
        excluirDocumento: function () {
            $3('#carregando-arquivo').fadeIn('slow');

            let self = this;
            $3.ajax({
                type: "GET",
                url: "/readequacao/readequacoes/excluir-documento",
                data: {
                    idDocumento: self.readequacao.idDocumento,
                    idPronac: self.idPronac,
                    idReadequacao: self.readequacao.idReadequacao
                }
            }).done(function (response) {
                self.mensagemSucesso("Documento excluido com sucesso.");
                self.readequacao.nomeArquivo = '';
                self.readequacao.idDocumento = '';
                self.$emit('eventoAtualizarReadequacao', self.readequacao);
                $3('#carregando-arquivo').fadeOut('slow');
            }).fail(function (response) {
                self.mensagemAlerta(response.mensagem);
                $3('#carregando-arquivo').fadeOut('slow');
            });
        }, validarDocumento: function (arquivo) {
            if (!this.arquivo.tiposAceitos.includes(arquivo.name.split(".").pop().toLowerCase())) {
                this.mensagemAlerta("Extens\xE3o de arquivo inv\xE1lida. Envie arquivos nos tipos: " + this.arquivo.tiposAceitos.join(','));
                return;
            }

            if (arquivo.size > this.arquivo.tamanhoMaximo) {
                this.mensagemAlerta("Arquivo ultrapassou o limite de " + this.arquivo.tamanhoMaximo);
                return;
            }
            return true;
        }
    },
});
