import axios from 'axios';
import * as outrasInformacoesHelperAPI from '@/helpers/api/OutrasInformacoes';
import * as actions from './actions';

jest.mock('axios');

describe('Outras Informacoes actions', () => {
    let commit;
    let mockReponse;

    describe('buscarCertidoesNegativas', () => {
        beforeEach(() => {
            mockReponse = {
                data: {
                    data: {
                        items: {
                            certidoes: {
                                dsCertidao: 'Quita&ccedil;&atilde;o de Tributos Federais',
                                CodigoCertidao: 49,
                                Pronac: 160059,
                            },
                        },
                    },
                },
            };

            axios.get.mockResolvedValue(mockReponse);

            commit = jest.fn();
            jest.spyOn(outrasInformacoesHelperAPI, 'buscarCertidoesNegativas');
            const idPronac = 216941;
            actions.buscarCertidoesNegativas({ commit }, idPronac);
        });

        test('it calls outrasInformacoesHelperAPI.buscarCertidoesNegativas', () => {
            expect(outrasInformacoesHelperAPI.buscarCertidoesNegativas).toHaveBeenCalled();
        });

        test('it is commit to buscarCertidoesNegativas', (done) => {
            const certidoesNegativas = mockReponse.data;
            done();
            expect(commit).toHaveBeenCalledWith('SET_CERTIDOES_NEGATIVAS', certidoesNegativas.data.items);
        });
    });

    describe('buscarDocumentosAssinados', () => {
        beforeEach(() => {
            mockReponse = {
                data: {
                    data: {
                        items: {
                            dsAtoAdministrativo: 'Parecer de Aprova&ccedil;&atilde;o Preliminar',
                            idDocumentoAssinatura: 3564,
                            pronac: 178894,
                        },
                    },
                },
            };

            axios.get.mockResolvedValue(mockReponse);

            commit = jest.fn();
            jest.spyOn(outrasInformacoesHelperAPI, 'buscarDocumentosAssinados');
            const idPronac = 216941;
            actions.buscarDocumentosAssinados({ commit }, idPronac);
        });

        test('it calls outrasInformacoesHelperAPI.buscarDocumentosAssinados', () => {
            expect(outrasInformacoesHelperAPI.buscarDocumentosAssinados).toHaveBeenCalled();
        });

        test('it is commit to buscarDocumentosAssinados', (done) => {
            const documentosAssinados = mockReponse.data;
            done();
            expect(commit).toHaveBeenCalledWith('SET_DOCUMENTOS_ASSINADOS', documentosAssinados.data.items);
        });
    });

    describe('buscarDadosComplementares', () => {
        beforeEach(() => {
            mockReponse = {
                data: {
                    data: {
                        items: {
                            CustosVinculados: {
                                Descricao: 'Custos de Administra&ccedil;&atilde;o',
                                Percentual: 15,
                            },
                            Proposta: {
                                Objetivos: 'Objetivo espec&iacute;fico do projeto &eacute; a realiza&ccedil;&atilde;o de tr&ecirc;s atra&ccedil;&otilde;es',
                            },
                        },
                    },
                },
            };

            axios.get.mockResolvedValue(mockReponse);

            commit = jest.fn();
            jest.spyOn(outrasInformacoesHelperAPI, 'buscarDadosComplementares');
            const idPronac = 216941;
            actions.buscarDadosComplementares({ commit }, idPronac);
        });

        test('it calls outrasInformacoesHelperAPI.buscarDadosComplementares', () => {
            expect(outrasInformacoesHelperAPI.buscarDadosComplementares).toHaveBeenCalled();
        });

        test('it is commit to buscarDadosComplementares', (done) => {
            const dadosComplementares = mockReponse.data;
            done();
            expect(commit).toHaveBeenCalledWith('SET_DADOS_COMPLEMENTARES', dadosComplementares.data.items);
        });
    });

    describe('buscarDocumentosAnexados', () => {
        beforeEach(() => {
            mockReponse = {
                data: {
                    data: {
                        items: {
                            documentos: {
                                Anexado: 'Documento do Proponente',
                                idArquivo: 180609,
                                AgenteDoc: 1,
                            },
                        },
                    },
                },
            };

            axios.get.mockResolvedValue(mockReponse);

            commit = jest.fn();
            jest.spyOn(outrasInformacoesHelperAPI, 'buscarDocumentosAnexados');
            const idPronac = 216941;
            actions.buscarDocumentosAnexados({ commit }, idPronac);
        });

        test('it calls outrasInformacoesHelperAPI.buscarDocumentosAnexados', () => {
            expect(outrasInformacoesHelperAPI.buscarDocumentosAnexados).toHaveBeenCalled();
        });

        test('it is commit to buscarDocumentosAnexados', (done) => {
            const documentosAnexados = mockReponse.data;
            done();
            expect(commit).toHaveBeenCalledWith('SET_DOCUMENTOS_ANEXADOS', documentosAnexados.data.items);
        });
    });

    describe('buscarLocalRealizacaoDeslocamento', () => {
        beforeEach(() => {
            mockReponse = {
                data: {
                    data: {
                        items: {
                            localRealizacoes: {
                                Descricao: 'Brasil',
                                UF: 'Santa Catarina',
                                Cidade: 'Conc&oacute;rdia',
                            },
                            Deslocamento: {
                                Qtde: 28,
                                PaisOrigem: 'Brasil',
                            },
                        },
                    },
                },
            };

            axios.get.mockResolvedValue(mockReponse);

            commit = jest.fn();
            jest.spyOn(outrasInformacoesHelperAPI, 'buscarLocalRealizacaoDeslocamento');
            const idPronac = 216941;
            actions.buscarLocalRealizacaoDeslocamento({ commit }, idPronac);
        });

        test('it calls outrasInformacoesHelperAPI.buscarLocalRealizacaoDeslocamento', () => {
            expect(outrasInformacoesHelperAPI.buscarLocalRealizacaoDeslocamento).toHaveBeenCalled();
        });

        test('it is commit to buscarLocalRealizacaoDeslocamento', (done) => {
            const localRealizacaoDeslocamento = mockReponse.data;
            done();
            expect(commit).toHaveBeenCalledWith('SET_LOCAL_REALIZACAO_DESLOCAMENTO', localRealizacaoDeslocamento.data.items);
        });
    });

    describe('buscarProvidenciaTomada', () => {
        beforeEach(() => {
            mockReponse = {
                data: {
                    data: {
                        items: {
                            providenciaTomada: {
                                Situacao: 'B01',
                                cnpjcpf: '08887383740',
                                ProvidenciaTomada: 'Proposta transformada em projeto cultural',
                            },
                        },
                    },
                },
            };

            axios.get.mockResolvedValue(mockReponse);

            commit = jest.fn();
            jest.spyOn(outrasInformacoesHelperAPI, 'buscarProvidenciaTomada');
            const idPronac = 216941;
            actions.buscarProvidenciaTomada({ commit }, idPronac);
        });

        test('it calls outrasInformacoesHelperAPI.buscarProvidenciaTomada', () => {
            expect(outrasInformacoesHelperAPI.buscarProvidenciaTomada).toHaveBeenCalled();
        });

        test('it is commit to buscarProvidenciaTomada', (done) => {
            const providenciaTomada = mockReponse.data;
            done();
            expect(commit).toHaveBeenCalledWith('SET_PROVIDENCIA_TOMADA', providenciaTomada.data.items);
        });
    });

    describe('buscarPlanoDistribuicaoIn2013', () => {
        beforeEach(() => {
            mockReponse = {
                data: {
                    data: {
                        items: {
                            idPlanoDistribuicao: 171982,
                            idProjeto: 207951,
                            idProduto: 3,
                        },
                    },
                },
            };

            axios.get.mockResolvedValue(mockReponse);

            commit = jest.fn();
            jest.spyOn(outrasInformacoesHelperAPI, 'buscarPlanoDistribuicaoIn2013');
            const idPronac = 194617;
            actions.buscarPlanoDistribuicaoIn2013({ commit }, idPronac);
        });

        test('it calls outrasInformacoesHelperAPI.buscarPlanoDistribuicaoIn2013', () => {
            expect(outrasInformacoesHelperAPI.buscarPlanoDistribuicaoIn2013).toHaveBeenCalled();
        });

        test('it is commit to buscarPlanoDistribuicaoIn2013', (done) => {
            const planoDistribuicaoIn2013 = mockReponse.data;
            done();
            expect(commit).toHaveBeenCalledWith('SET_PLANO_DISTRIBUICAO_IN2013', planoDistribuicaoIn2013.data.items);
        });
    });

    describe('buscarHistoricoEncaminhamento', () => {
        beforeEach(() => {
            mockReponse = {
                data: {
                    data: {
                        items: {
                            Encaminhamentos: {
                                Unidade: 'FUNARTE',
                                DtEnvio: '03/04/2018 00:00:00',
                                qtDias: 44,
                            },
                        },
                    },
                },
            };

            axios.get.mockResolvedValue(mockReponse);

            commit = jest.fn();
            jest.spyOn(outrasInformacoesHelperAPI, 'buscarHistoricoEncaminhamento');
            const idPronac = 216941;
            actions.buscarHistoricoEncaminhamento({ commit }, idPronac);
        });

        test('it calls outrasInformacoesHelperAPI.buscarHistoricoEncaminhamento', () => {
            expect(outrasInformacoesHelperAPI.buscarHistoricoEncaminhamento).toHaveBeenCalled();
        });

        test('it is commit to buscarHistoricoEncaminhamento', (done) => {
            const historicoEncaminhamento = mockReponse.data;
            done();
            expect(commit).toHaveBeenCalledWith('SET_HISTORICO_ENCAMINHAMENTO', historicoEncaminhamento.data.items);
        });
    });

    describe('buscarTramitacaoDocumento', () => {
        beforeEach(() => {
            mockReponse = {
                data: {
                    data: {
                        items: {
                            dsTipoDocumento: 'Comunicado de Mecenato',
                            idDocumento: 453659,
                            idLote: 295184,
                        },
                    },
                },
            };

            axios.get.mockResolvedValue(mockReponse);

            commit = jest.fn();
            jest.spyOn(outrasInformacoesHelperAPI, 'buscarTramitacaoDocumento');
            const idPronac = 216941;
            actions.buscarTramitacaoDocumento({ commit }, idPronac);
        });

        test('it calls outrasInformacoesHelperAPI.buscarTramitacaoDocumento', () => {
            expect(outrasInformacoesHelperAPI.buscarTramitacaoDocumento).toHaveBeenCalled();
        });

        test('it is commit to buscarTramitacaoDocumento', (done) => {
            const tramitacaoDocumento = mockReponse.data;
            done();
            expect(commit).toHaveBeenCalledWith('SET_TRAMITACAO_DOCUMENTO', tramitacaoDocumento.data.items);
        });
    });

    describe('buscarTramitacaoProjeto', () => {
        beforeEach(() => {
            mockReponse = {
                data: {
                    data: {
                        items: {
                            Situacao: 'Cadastrado',
                            Origem: 'SEFIC/GEAAP/SUAPI/DIAAPI',
                            Destino: 'SEFIC/GEAR/SACAV',
                        },
                    },
                },
            };

            axios.get.mockResolvedValue(mockReponse);

            commit = jest.fn();
            jest.spyOn(outrasInformacoesHelperAPI, 'buscarTramitacaoProjeto');
            const idPronac = 216941;
            actions.buscarTramitacaoProjeto({ commit }, idPronac);
        });

        test('it calls outrasInformacoesHelperAPI.buscarTramitacaoProjeto', () => {
            expect(outrasInformacoesHelperAPI.buscarTramitacaoProjeto).toHaveBeenCalled();
        });

        test('it is commit to buscarTramitacaoProjeto', (done) => {
            const tramitacaoProjeto = mockReponse.data;
            done();
            expect(commit).toHaveBeenCalledWith('SET_TRAMITACAO_PROJETO', tramitacaoProjeto.data.items);
        });
    });

    describe('buscarUltimaTramitacao', () => {
        beforeEach(() => {
            mockReponse = {
                data: {
                    data: {
                        items: {
                            Emissor: 'M&ordf; do Socorro Silva',
                            Receptor: 'Renata L.Oliveira',
                            Estado: 'Recebido',
                        },
                    },
                },
            };

            axios.get.mockResolvedValue(mockReponse);

            commit = jest.fn();
            jest.spyOn(outrasInformacoesHelperAPI, 'buscarUltimaTramitacao');
            const idPronac = 216941;
            actions.buscarUltimaTramitacao({ commit }, idPronac);
        });

        test('it calls outrasInformacoesHelperAPI.buscarUltimaTramitacao', () => {
            expect(outrasInformacoesHelperAPI.buscarUltimaTramitacao).toHaveBeenCalled();
        });

        test('it is commit to buscarUltimaTramitacao', (done) => {
            const ultimaTramitacao = mockReponse.data;
            done();
            expect(commit).toHaveBeenCalledWith('SET_ULTIMA_TRAMITACAO', ultimaTramitacao.data.items);
        });
    });

    describe('buscarPlanoDistribuicaoIn2017', () => {
        beforeEach(() => {
            mockReponse = {
                data: {
                    data: {
                        items: {
                            planodistribuicaoproduto: {
                                idPlanoDistribuicao: 229891,
                                idProjeto: 273246,
                                idProduto: 19,
                            },
                        },
                    },
                },
            };

            axios.get.mockResolvedValue(mockReponse);

            commit = jest.fn();
            jest.spyOn(outrasInformacoesHelperAPI, 'buscarPlanoDistribuicaoIn2017');
            const idPronac = 216941;
            actions.buscarPlanoDistribuicaoIn2017({ commit }, idPronac);
        });

        test('it calls outrasInformacoesHelperAPI.buscarPlanoDistribuicaoIn2017', () => {
            expect(outrasInformacoesHelperAPI.buscarPlanoDistribuicaoIn2017).toHaveBeenCalled();
        });

        test('it is commit to buscarPlanoDistribuicaoIn2017', (done) => {
            const planoDistribuicaoIn2017 = mockReponse.data;
            done();
            expect(commit).toHaveBeenCalledWith('SET_PLANO_DISTRIBUICAO_IN2017', planoDistribuicaoIn2017.data.items);
        });
    });

    describe('buscarDiligenciaProposta', () => {
        beforeEach(() => {
            mockReponse = {
                data: {
                    data: {
                        items: {
                            nomeProjeto: 'FOTOATIVIDADES',
                            dataSolicitacao: '04/04/2017',
                        },
                    },
                },
            };

            axios.get.mockResolvedValue(mockReponse);

            commit = jest.fn();
            jest.spyOn(outrasInformacoesHelperAPI, 'buscarDiligenciaProposta');
            const idPreProjeto = 245047;
            const idAvaliacaoProposta = 407842;
            const value = { idPreProjeto, idAvaliacaoProposta };

            actions.buscarDiligenciaProposta({ commit }, value);
        });

        test('it calls outrasInformacoesHelperAPI.buscarDiligenciaProposta', () => {
            expect(outrasInformacoesHelperAPI.buscarDiligenciaProposta).toHaveBeenCalled();
        });

        test('it is commit to buscarDiligenciaProposta', (done) => {
            const diligenciaProposta = mockReponse.data;
            done();
            expect(commit).toHaveBeenCalledWith('SET_DILIGENCIA_PROPOSTA', diligenciaProposta.data.items);
        });
    });

    describe('buscarDiligenciaAdequacao', () => {
        beforeEach(() => {
            mockReponse = {
                data: {
                    data: {
                        items: {
                            idAvaliarAdequacaoProjeto: 1452,
                            dtAvaliacao: '06/06/2018',
                        },
                    },
                },
            };

            axios.get.mockResolvedValue(mockReponse);

            commit = jest.fn();
            jest.spyOn(outrasInformacoesHelperAPI, 'buscarDiligenciaAdequacao');
            const idPronac = 209561;
            const idAvaliarAdequacaoProjeto = 1452;
            const value = { idPronac, idAvaliarAdequacaoProjeto };
            actions.buscarDiligenciaAdequacao({ commit }, value);
        });

        test('it calls outrasInformacoesHelperAPI.buscarDiligenciaAdequacao', () => {
            expect(outrasInformacoesHelperAPI.buscarDiligenciaAdequacao).toHaveBeenCalled();
        });

        test('it is commit to buscarDiligenciaAdequacao', (done) => {
            const diligenciaAdequacao = mockReponse.data;
            done();
            expect(commit).toHaveBeenCalledWith('SET_DILIGENCIA_ADEQUACAO', diligenciaAdequacao.data.items);
        });
    });

    describe('buscarDiligenciaProjeto', () => {
        beforeEach(() => {
            mockReponse = {
                data: {
                    data: {
                        items: {
                            arquivos: {
                                idArquivo: 1272611,
                            },
                            nomeProjeto: 'FOTOATIVIDADES',
                        },
                    },
                },
            };

            axios.get.mockResolvedValue(mockReponse);

            commit = jest.fn();
            jest.spyOn(outrasInformacoesHelperAPI, 'buscarDiligenciaProjeto');
            const idPronac = 209561;
            const idDiligencia = 72427;
            const value = { idPronac, idDiligencia };
            actions.buscarDiligenciaProjeto({ commit }, value);
        });

        test('it calls outrasInformacoesHelperAPI.buscarDiligenciaProjeto', () => {
            expect(outrasInformacoesHelperAPI.buscarDiligenciaProjeto).toHaveBeenCalled();
        });

        test('it is commit to buscarDiligenciaProjeto', (done) => {
            const diligenciaProjeto = mockReponse.data;
            done();
            expect(commit).toHaveBeenCalledWith('SET_DILIGENCIA_PROJETO', diligenciaProjeto.data.items);
        });
    });

    describe('buscarDiligencia', () => {
        beforeEach(() => {
            mockReponse = {
                data: {
                    data: {
                        items: {
                            diligenciaAdequacao: {
                                tipoDiligencia: 'Dilig&ecirc;ncia na An&aacute;lise da adequa&ccedil;&atilde;o &agrave; realidade do projeto.',
                                idAvaliarAdequacaoProjeto: 1452,
                            },
                            diligenciaProjeto: {
                                tipoDiligencia: 'Dilig&ecirc;ncia de Checklist - An&aacute;lise',
                                idDiligencia: 72427,
                            },
                            diligenciaProposta: {
                                idAvaliacaoProposta: 401888,
                                idPreprojeto: 245047,
                            },
                        },
                    },
                },
            };

            axios.get.mockResolvedValue(mockReponse);

            commit = jest.fn();
            jest.spyOn(outrasInformacoesHelperAPI, 'buscarDiligencia');
            const idPronac = 216941;
            actions.buscarDiligencia({ commit }, idPronac);
        });

        test('it calls outrasInformacoesHelperAPI.buscarDiligencia', () => {
            expect(outrasInformacoesHelperAPI.buscarDiligencia).toHaveBeenCalled();
        });

        test('it is commit to buscarDiligencia', (done) => {
            const diligencia = mockReponse.data;
            done();
            expect(commit).toHaveBeenCalledWith('SET_DILIGENCIA', diligencia.data.items);
        });
    });
});
