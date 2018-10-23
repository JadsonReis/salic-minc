import * as layoutHelperAPI from '@/helpers/api/Layout';
import * as solicitacaoHelperAPI from '@/helpers/api/Solicitacao';
import * as desencapsularResponse from '@/helpers/actions';
import * as types from './types';

export const buscarPerfisDisponiveis = ({ commit }, params) => {
    layoutHelperAPI.buscarPerfisDisponiveis(params)
        .then((response) => {
            const items = desencapsularResponse.default(response);
            commit(types.SET_PERFIS_DISPONIVEIS, items);
        });
};

export const alterarPerfil = (_, perfil) => {
    const grupoAtivo = perfil.gru_codigo;
    const orgaoAtivo = perfil.uog_orgao;
    const url = `/autenticacao/perfil/alterarperfil?codGrupo=${grupoAtivo}&codOrgao=${orgaoAtivo}`;
    window.location.replace(url);
};

export const obterSolicitacoes = ({ commit }) => {
    solicitacaoHelperAPI.obterSolicitacoes()
        .then((response) => {
            const items = desencapsularResponse.default(response);
            commit(types.SET_SOLICITACOES, items);
        });
};

export const buscarVersao = ({ commit }) => {
    layoutHelperAPI.buscarVersao()
        .then((response) => {
            const versao = desencapsularResponse.default(response);
            commit(types.SET_VERSAO, versao[0]);
        });
};
