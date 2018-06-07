<?php

class PrestacaoContas_GerenciarController extends MinC_Controller_Action_Abstract
{
    private $tipoDocumento = array(' - Selecione - ','Cupom Fiscal','Guia de Recolhimento','Nota Fiscal/Fatura','Recibo de Pagamento','RPA');

    public function init()
    {
        parent::init();
    }

    public function comprovarAction()
    {
        $idPronac = $this->getRequest()->getParam('idpronac');

        $idPlanilhaAprovacao = $this->getRequest()->getParam('idPlanilhaAprovacao');
        $idPlanilhaItens = $this->getRequest()->getParam('idPlanilhaItens');
        $idComprovantePagamento = $this->getRequest()->getParam('idComprovantePagamento');
        $uf = $this->getRequest()->getParam('uf');
        $cdproduto = $this->getRequest()->getParam('produto');
        $cdcidade = $this->getRequest()->getParam('cidade');
        $cdetapa = $this->getRequest()->getParam('etapa');

        $diligencia = new Diligencia();
        $diligencia = $diligencia->aberta($idPronac);

        $this->view->diligenciaTodosItens = $diligencia;

        $idPlanilhaAprovacao = $this->getRequest()->getParam('idPlanilhaAprovacao');

        //Adicionado para ser usado como novo parametro do metodo pesquisarComprovantePorItem
        $idComprovantePagamento = $this->getRequest()->getParam('idComprovantePagamento');

        $planilhaItemModel = new PlanilhaItem();
        $produtoModel = new Produto();
        $etapaModel = new PlanilhaEtapa();
        $itemModel = new PlanilhaItem();

        $itemPlanilhaAprovacao = $planilhaItemModel->buscarItemDaAprovacao($idPlanilhaAprovacao);

        /*todo*/
        $planilhaAprovacao = new PlanilhaAprovacao();
        $valoresItem = $planilhaAprovacao->planilhaAprovada(
            $idPronac,
            $uf,
            $cdetapa,
            $cdproduto,
            $cdcidade,
            null,
            $idPlanilhaItens
        );
        $this->view->valores = $valoresItem->current();
        /*todos*/

        $produto = $produtoModel->find($itemPlanilhaAprovacao->idProduto)->current();
        $etapa = $etapaModel->find($itemPlanilhaAprovacao->idEtapa)->current();
        $item = $itemModel->find($itemPlanilhaAprovacao->idPlanilhaItem)->current();

        $fornecedorModel = new FornecedorModel();
        $fornecedor = $fornecedorModel->pesquisarFornecedorItem($idPlanilhaAprovacao);

        if ($fornecedor) {
            $fornecedor = (object) array_map('utf8_encode', $fornecedor);

            $cpfCnpj = $fornecedor->CNPJCPF;
            $fornecedorUsaCnpj = 14 == strlen($cpfCnpj);
            $fornecedor->CNPJCPF = $fornecedorUsaCnpj ? Mascara::addMaskCNPJ($cpfCnpj) : Mascara::addMaskCPF($cpfCnpj);
            $fornecedor->usaCnpj = $fornecedorUsaCnpj;
            $this->view->fornecedor = $fornecedor;
        }

        $comprovantePagamentoModel = new ComprovantePagamento();
        /* $comprovantesDePagamento = $comprovantePagamentoModel->pesquisarComprovantePorItem($item->idPlanilhaItens, $idPronac, $etapa->idPlanilhaEtapa, $itemPlanilhaAprovacao->idProduto, $itemPlanilhaAprovacao->idUFDespesa, $itemPlanilhaAprovacao->idMunicipioDespesa); //ID Recuperado */

        $comprovantesDePagamento = $planilhaAprovacao->vwComprovacaoFinanceiraProjetoPorItemOrcamentario(
                $idPronac,
                $idPlanilhaItens,
                null,
                $cdproduto,
                null,
                $cdcidade
            )->toArray();

        array_walk($comprovantesDePagamento, function (&$comprovanteDePagamento) use ($fornecedorModel) {
            $comprovanteDePagamento = (object) $comprovanteDePagamento;

            if ($comprovanteDePagamento->idFornecedor) {
                $fornecedor = $fornecedorModel
                    ->find($comprovanteDePagamento->idFornecedor)
                    ->current();
                if ($fornecedor) {
                    $cpfCnpj = $fornecedor->CNPJCPF;
                    $fornecedorUsaCnpj = 14 == strlen($cpfCnpj);
                    $fornecedor->CNPJCPF = $fornecedorUsaCnpj ? Mascara::addMaskCNPJ($cpfCnpj) : Mascara::addMaskCPF($cpfCnpj);
                }
            } elseif ($comprovanteDePagamento->idFornecedorExterior) {
                $fornecedor = new stdClass();
                $fornecedor->CNPJCPF = '<em>Fornecedor estrangeiro</em>';
            }

            $comprovanteDePagamento->fornecedor = $fornecedor;
            unset($comprovanteDePagamento->idFornecedor);
        });

        $pais = new Pais();
        $paises = $pais->buscar(array(), 'Descricao');

        $this->view->produto = $produto;
        $this->view->etapa = $etapa;
        $this->view->item = $item;
        $this->view->itemPlanilhaAprovacao = $itemPlanilhaAprovacao;
        $this->view->comprovantes = $comprovantesDePagamento;
        $this->view->paises = $paises;

        if ($this->getRequest()->isPost()) {
            $this->view->vlComprovado = filter_input(INPUT_POST, 'vlComprovado');
            $this->view->idAgente = filter_input(INPUT_POST, 'idAgente');
            $this->view->CNPJCPF = filter_input(INPUT_POST, 'CNPJCPF');
            $this->view->Descricao = filter_input(INPUT_POST, 'Descricao');
            $this->view->tpDocumento = filter_input(INPUT_POST, 'tpDocumento');
            $this->view->nrComprovante = filter_input(INPUT_POST, 'nrComprovante');
            $this->view->nrSerie = filter_input(INPUT_POST, 'nrSerie');
            $this->view->dtEmissao = filter_input(INPUT_POST, 'dtEmissao');
            $this->view->tpFormaDePagamento = filter_input(INPUT_POST, 'tpFormaDePagamento');
            $this->view->nrDocumentoDePagamento = filter_input(INPUT_POST, 'nrDocumentoDePagamento');
            $this->view->dsJustificativa = filter_input(INPUT_POST, 'dsJustificativa');
        } elseif ($idComprovantePagamento) {
            $comprovanteAtualizar = current($comprovantePagamentoModel->pesquisarComprovante($idComprovantePagamento));

            $this->view->idComprovantePagamento = $idComprovantePagamento;
            $this->view->vlComprovacao = $comprovanteAtualizar['vlComprovacao'];

            if ($comprovanteAtualizar['idFornecedor']) {
                $fornecedorModel = new FornecedorModel();
                $fornecedor = $fornecedorModel->pesquisarFornecedor($comprovanteAtualizar['idFornecedor']);
                $this->view->paisFornecedor = 'Brasil';
                $this->view->exterior = false;
                $this->view->idAgente = $comprovanteAtualizar['idFornecedor'];
                $fornecedor->usaCnpj = 14 == strlen($fornecedor->CNPJCPF);
                $this->view->idPlanilhaAprovacao = $idPlanilhaAprovacao;
                $this->view->CNPJCPF = $fornecedor->CNPJCPF;
                $this->view->fornecedor = $fornecedor;
                $this->view->Descricao = $fornecedor->Descricao;
                $this->view->tpDocumento = $comprovanteAtualizar['tpDocumento'];
                $this->view->idArquivo = $comprovanteAtualizar['idArquivo'];
                $this->view->nomeArquivo = $comprovanteAtualizar['nmArquivo'];
                $this->view->nrComprovante = $comprovanteAtualizar['nrComprovante'];
                $this->view->nrSerie = $comprovanteAtualizar['nrSerie'];
                $this->view->dtEmissao = $comprovanteAtualizar['dtEmissao'];
                $this->view->dtPagamento = $comprovanteAtualizar['dtPagamento'];
                $this->view->tpFormaDePagamento = $comprovanteAtualizar['tpFormaDePagamento'];
                $this->view->nrDocumentoDePagamento = $comprovanteAtualizar['nrDocumentoDePagamento'];
                $this->view->JustificativaTecnico = $comprovanteAtualizar['JustificativaTecnico'];
                $this->view->dsJustificativa = $comprovanteAtualizar['dsJustificativa'];
            } elseif ($comprovanteAtualizar['idFornecedorExterior']) {
                $fornecedorInvoice = new FornecedorInvoice();

                $where = array();
                $where['idFornecedorExterior = ? '] = $comprovanteAtualizar['idFornecedorExterior'];
                $fornecedor = $fornecedorInvoice->buscar($where);
                $this->view->paisFornecedor = $fornecedor[0]->dsNome;
                $this->view->exterior = true;

                $this->view->idAgente = $comprovanteAtualizar['idFornecedorExterior'];
                $this->view->idPlanilhaAprovacao = $idPlanilhaAprovacao;
                $this->view->fornecedor = $fornecedor;
                $this->view->tpDocumento = $comprovanteAtualizar['tpDocumento'];
                $this->view->idArquivo = $comprovanteAtualizar['idArquivo'];
                $this->view->nomeArquivo = $comprovanteAtualizar['nmArquivo'];
                $this->view->nrComprovante = $comprovanteAtualizar['nrComprovante'];
                $this->view->nrSerie = $comprovanteAtualizar['nrSerie'];
                $this->view->dtPagamento = $comprovanteAtualizar['dtPagamento'];
                $this->view->dtEmissao = $comprovanteAtualizar['dtEmissao'];
                $this->view->tpFormaDePagamento = $comprovanteAtualizar['tpFormaDePagamento'];
                $this->view->nrDocumentoDePagamento = $comprovanteAtualizar['nrDocumentoDePagamento'];
                $this->view->JustificativaTecnico = $comprovanteAtualizar['JustificativaTecnico'];
                $this->view->dsJustificativa = $comprovanteAtualizar['dsJustificativa'];
            }
        }

        $tblProjetos = new Projetos();
        $projeto = $tblProjetos->buscarTodosDadosProjeto($idPronac);
        $this->view->situacao = $projeto->current()->Situacao;

        $this->view->idpronac = $this->getRequest()->getParam('idpronac');
        $this->view->tipoDocumentoConteudo = $this->tipoDocumento;

        $this->view->uf = $this->getRequest()->getParam('uf');
        $this->view->cdproduto = $this->getRequest()->getParam('produto');
        $this->view->cdcidade = $this->getRequest()->getParam('cidade');
        $this->view->cdetapa = $this->getRequest()->getParam('etapa');
        $this->view->idplanilhaitens = $this->getRequest()->getParam('idPlanilhaItens');
    }

    public function cadastrarcomprovacaopagamentoAction()
    {
        $dtPagamento = $this->getRequest()->getParam('dtPagamento') ? new DateTime(data::dataAmericana($this->getRequest()->getParam('dtPagamento'))) : null;

        if (empty($dtPagamento)) {
            throw new Exception('Erro no preenchimento.');
        }

        try {
            $this->verificarPermissaoAcesso(false, true, false);
            $request = $this->getRequest();

            $pais = $this->getRequest()->getParam('pais');

            if (empty($pais)) {
                throw new Exception('Por favor inserir um arquivo com tamanho máximo de 5MB."');
            }

            $arquivoModel = new ArquivoModel();
            if ($pais == 'Brasil') {
                $arquivoModel->cadastrar('arquivo');
                $idArquivo = $arquivoModel->getId();
                if (empty($idArquivo)) {
                    throw new Exception('O arquivo deve ser PDF.');
                }
                $comprovantePagamentoModel = new ComprovantePagamento(
                    null,
                    $request->getParam('idAgente'),
                    $request->getParam('itemId'),
                    $request->getParam('tpDocumento'),
                    $request->getParam('nrComprovante'),
                    $request->getParam('nrSerie'),
                    $request->getParam('dtEmissao') ? new DateTime(data::dataAmericana($request->getParam('dtEmissao'))) : null,
                    $arquivoModel->getId(),
                    $request->getParam('tpFormaDePagamento'),
                    $dtPagamento,
                    str_replace(',', '.', str_replace('.', '', $request->getParam('vlComprovado'))),
                    $request->getParam('nrDocumentoDePagamento'),
                    $request->getParam('dsJustificativa')
                );
            } else {
                $arquivoModel->cadastrar('arquivoInternacional');
                $idArquivo = $arquivoModel->getId();
                if (empty($idArquivo)) {
                    throw new Exception('O arquivo deve ser PDF.');
                }
                $comprovantePagamentoModel = new ComprovantePagamentoInvoice(
                    null,
                    new FornecedorInvoice(
                        null,
                        $pais,
                        $request->getParam('nomeRazaoSocialInternacional'),
                        $request->getParam('enderecoInternacional')
                        ),
                    $request->getParam('itemId'),
                    $request->getParam('nif'),
                    $request->getParam('nrSerieInternacional'),
                    $request->getParam('dtEmissaoInternacional') ? new DateTime(data::dataAmericana($request->getParam('dtEmissaoInternacional'))) : null,
                    $arquivoModel->getId(),
                    new DateTime(),
                    str_replace(',', '.', str_replace('.', '', $request->getParam('vlComprovadoInternacional'))),
                    $request->getParam('dsJustificativaInternacional')
                );
            }

            $comprovantePagamentoModel->cadastrar();
            $this->_helper->flashMessenger('Comprovante cadastrado com sucesso.');
            $this->_helper->flashMessengerType('CONFIRM');
            $this->redirect(
                str_replace(
                    $this->view->baseUrl(),
                    '',
                    $this->view->url(
                        array(
                            'module' => 'prestacao-contas',
                            'controller' => 'gerenciar',
                            'action' => 'comprovar',
                            'idusuario' => $this->view->idusuario,
                            'idpronac' => $request->getParam('idpronac'),
                        )
                    )
                )
            );
        } catch (Exception $e) {
            $this->view->message = $e->getMessage();
            $this->view->message_type = 'ERROR';
            $this->forward('comprovar');
        }
    }

    public function atualizarcomprovacaopagamentoAction()
    {
        $request = $this->getRequest();
        $idPronac = $request->getParam('idpronac');
        $idComprovantePagamento = $request->getParam('idComprovantePagamento');
        $vlComprovadoNovo = (float) str_replace(',', '.', str_replace('.', '', $request->getParam('vlComprovado')));

        /*todos*/
        $planilhaAprovacao = new PlanilhaAprovacao();
        $planilhaAprovacaoItem = $planilhaAprovacao->vwComprovacaoFinanceiraProjetoPorItemOrcamentario(
            $idPronac,
            null,
            null,
            null,
            $idComprovantePagamento
        );

        $valorComprovadoAntigo = $planilhaAprovacaoItem->current()->vlComprovacao;
        $valoresItem = $planilhaAprovacao->vwComprovacaoFinanceiraProjeto(
            $idPronac,
            null,
            $planilhaAprovacaoItem->current()->cdEtapa,
            $planilhaAprovacaoItem->current()->cdProduto,
            $planilhaAprovacaoItem->current()->cdCidade,
            null,
            $planilhaAprovacaoItem->current()->idPlanilhaItem
        );
        $this->view->valores = $valoresItem->current();

        $valorAprovadoAtual = $valoresItem->current()->vlAprovado;
        $valorComprovadoAtual = $valoresItem->current()->vlComprovado;

        $valorComprovadoNovo = ($valorComprovadoAtual - $valorComprovadoAntigo) + $vlComprovadoNovo;

        if ($valorComprovadoNovo > $valorAprovadoAtual) {
            throw new Exception('Valor comprovado acima do permitido!');
        }

        try {
            //$this->verificarPermissaoAcesso(false, true, false);
            $pais = $request->getParam('pais');

            $paginaRedirecionar = $request->getParam('paginaRedirecionar');
            $redirectsValidos = array('comprovacaopagamento', 'gerenciar');
            if (!in_array($paginaRedirecionar, $redirectsValidos)) {
                $paginaRedirecionar = 'comprovantes-recusados';
            }

            if ($pais == 'Brasil') {
                $comprovantePagamentoModel = new ComprovantePagamento();
                $comprovantePagamento = $comprovantePagamentoModel->find($idComprovantePagamento)->current();

                $comprovantePagamentoModel = new ComprovantePagamento(
                    $idComprovantePagamento,
                    $request->getParam('idAgente'),
                    $request->getParam('itemId'),
                    $request->getParam('tpDocumento'),
                    $request->getParam('nrComprovante'),
                    $request->getParam('nrSerie'),
                    $request->getParam('dtEmissao') ? new DateTime(data::dataAmericana($request->getParam('dtEmissao'))) : null,
                    $comprovantePagamento->idArquivo,
                    $request->getParam('tpFormaDePagamento'),
                    $request->getParam('dtPagamento') ? new DateTime(data::dataAmericana($request->getParam('dtPagamento'))) : null,
                    str_replace(',', '.', str_replace('.', '', $request->getParam('vlComprovado'))),
                    $request->getParam('nrDocumentoDePagamento'),
                    $request->getParam('dsJustificativa')
                );
                if ($_FILES['arquivo']['name'] != '') {
                    $comprovantePagamentoModel->atualizar(4, true);
                } else {
                    // nao atualiza arquivo se n�o houver novo upload
                    $comprovantePagamentoModel->atualizar(4);
                }

                // internacional
            } else {

                // verificar se alterou alguma coisa do idAgente
                $fornecedorInternacional = new FornecedorInvoice();

                $dadosFornecedor = array();
                $dadosFornecedor['dsNome'] = $pais;
                $dadosFornecedor['dsEndereco'] = $request->getParam('nomeRazaoSocialInternacional');
                $dadosFornecedor['dsPais'] = $request->getParam('enderecoInternacional');

                $fornecedorInternacional->update(
                    $dadosFornecedor,
                    sprintf('idFornecedorExterior = %d', $request->getParam('idAgente'))
                );

                $comprovantePagamentoModel = new ComprovantePagamentoInvoice(
                    $idComprovantePagamento,
                    $request->getParam('idAgente'),
                    $request->getParam('itemId'),
                    $request->getParam('nif'),
                    $request->getParam('nrSerieInternacional'),
                    $request->getParam('dtEmissaoInternacional') ? new DateTime(data::dataAmericana($request->getParam('dtEmissaoInternacional'))) : null,
                    $request->getParam('arquivo_edit'),
                    new DateTime(),
                    str_replace(',', '.', str_replace('.', '', $request->getParam('vlComprovadoInternacional'))),
                    $request->getParam('dsJustificativaInternacional')
                );

                if ($_FILES['arquivoInternacional']['name'] != '') {
                    $comprovantePagamentoModel->atualizar(4, true);
                } else {
                    // nao atualiza arquivo se n�o houver novo upload
                    $comprovantePagamentoModel->atualizar(4);
                }
            }

            # View Parameters
            $this->view->comprovantePagamento = $comprovantePagamentoModel->toStdclass();

            $this->_helper->flashMessenger('Comprovante enviado com sucesso.');
            $this->_helper->flashMessengerType('CONFIRM');
            if ($paginaRedirecionar == 'gerenciar') {
                $this->redirect(
                    str_replace(
                        $this->view->baseUrl(),
                        '',
                        $this->view->url(
                            array(
                                'module' => 'prestacao-contas',
                                'controller' => 'gerenciar',
                                'action' => 'comprovar',
                                'idusuario' => $this->view->idusuario,
                                'idpronac' => $request->getParam('idpronac'),
                                'idPlanilhaAprovacao' => $request->getParam('idPlanilhaAprovacao'),
                            ),
                            null,
                            true
                        )
                    )
                );
                return;
            }
            $this->redirect(
                str_replace(
                    $this->view->baseUrl(),
                    '',
                    $this->view->url(
                        array(
                            'controller' => 'comprovarexecucaofinanceira',
                            'action' => $paginaRedirecionar,
                            'idusuario' => $this->view->idusuario,
                            'idpronac' => $request->getParam('idpronac'),
                            'idPlanilhaAprovacao' => $request->getParam('idPlanilhaAprovacao'),
                        ),
                        null,
                        true
                    )
                )
            );
        } catch (Exception $e) {
            $message = $e->getMessage();
            if (strpos($e->getMessage(), 'DateTime::__construct()') !== false) {
                $message = 'Data de emissao inv�lida!';
            }
            $this->view->message = $message;
            $this->view->message_type = 'ERROR';
            $this->forward('comprovacaopagamento-recusado');
        }
    }

    public function comprovarPagamentoAction()
    { }

    public function cadastrarAction()
    {
        $comprovante = new PrestacaoContas_Model_ComprovantePagamento();

        $comprovante->preencher($this->getRequest()->getPost()['comprovante']);

            /* $request->getParam('tpFormaDePagamento'), */
            /* str_replace(',', '.', str_replace('.', '', $request->getParam('vlComprovado'))), */
            /* $request->getParam('nrDocumentoDePagamento'), */
        $data = [];
        try {
            $id = $comprovante->cadastrar();
            $data = ['success' => true, 'idComprovantePagamento' => $id];
        } catch (Exception $e) {
            $this->view->message = $e->getMessage();
        }
        $this->_helper->json($data);
    }

    public function fornecedorAction()
    {
        $this->_helper->layout->disableLayout();

        $agentesDao = new Agente_Model_DbTable_Agentes();

        $cnpjcpf = preg_replace('/\.|-|\/|\?/', '', utf8_decode($this->getRequest()->getParam('cnpjcpf')));

        $fornecedor = $agentesDao->buscarFornecedor(array(' A.CNPJCPF = ? '=>$cnpjcpf))->current();

        if ($fornecedor) {
            $fornecedor = array(
                'retorno'=>true,
                'idAgente'=>$fornecedor->idAgente,
                'nome'=>utf8_encode($fornecedor->nome)
            );
        } else {
            $fornecedor = array('retorno'=>false, 'CNPJCPF'=>$cnpjcpf);
        }

        $this->_helper->json($fornecedor);
    }

    public function paisAction()
    {
        $this->_helper->layout->disableLayout();

        $pais = new Pais();

        $paises = $pais->buscar();
        $data = [];
        foreach($paises as $key => $pais) {
            $data[$key]['id']  = $pais['idPais'];
            $data[$key]['nome']  = utf8_encode($pais['Descricao']);
        }

        $this->_helper->json($data);
    }
}
