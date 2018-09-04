import * as api from './base';

export const buscaProjeto = (idPronac) => {
    const module = '/projeto';
    const controller = '/projeto';
    const action = '/get';
    const queryParams = `?idPronac=${idPronac}`;
    return api.getRequest(`${module}${controller}${action}`, queryParams);
};

export const buscaProjetoIncentivo = (idPronac) => {
    const module = '/projeto';
    const controller = '/incentivo';
    const action = '/obter-projeto-ajax';
    const queryParams = `?idPronac=${idPronac}`;
    return api.getRequest(`${module}${controller}${action}`, queryParams);
};

export const buscaProjetoConvenio = (idPronac) => {
    const module = '/projeto';
    const controller = '/incentivo';
    const action = '/obter-projeto-ajax';
    const queryParams = `?idPronac=${idPronac}`;
    return api.getRequest(`${module}${controller}${action}`, queryParams);
};

export const buscarTransferenciaRecursos = (idPronac, acao) => {
    const module = '/readequacao';
    const controller = '/transferencia-recursos-rest';
    const action = '/index';
    const queryParams = `?idPronac=${idPronac}&acao=${acao}`;
    return api.getRequest(`${module}${controller}${action}`, queryParams);
};

export const buscaProponente = (idPronac) => {
    const path = '/projeto/proponente-rest';
    const queryParams = `/idPronac/${idPronac}`;
    return api.getRequest(path, queryParams);
};
