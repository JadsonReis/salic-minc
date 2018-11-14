<?php

namespace Application\Modules\Execucao\Service\DadosReadequacoes;

class DadosReadequacoes
{
    /**
     * @var \Zend_Controller_Request_Abstract $request
     */
    private $request;

    /**
     * @var \Zend_Controller_Response_Abstract $response
     */
    private $response;

    function __construct($request, $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function buscarReadequacoes()
    {
        $idPronac = $this->request->idPronac;

        if (strlen($idPronac) > 7) {
            $idPronac = \Seguranca::dencrypt($idPronac);
        }
        if (!empty($idPronac)) {
//            $tblProjeto = new \Projetos();
//            $rsProjeto = $tblProjeto->buscar(array("IdPronac=?"=>$idPronac))->current();
//            $pronac = $rsProjeto->AnoProjeto.$rsProjeto->Sequencial;

            $Readequacao_Model_DbTable_TbReadequacao = new \Readequacao_Model_DbTable_TbReadequacao();
            $dadosReadequacoes = $Readequacao_Model_DbTable_TbReadequacao->buscarDadosReadequacoes(array('a.idPronac = ?'=>$idPronac, 'a.siEncaminhamento <> ?' => 12))->toArray();

            $dadosReadequacoesDevolvidas = $Readequacao_Model_DbTable_TbReadequacao->buscarDadosReadequacoes(array('a.idPronac = ?'=>$idPronac, 'a.siEncaminhamento = ?' => 12, 'a.stAtendimento = ?' => 'E', 'a.stEstado = ?' => 0))->toArray();

            $tbReadequacaoXParecer = new \Readequacao_Model_DbTable_TbReadequacaoXParecer();
            foreach ($dadosReadequacoes as &$dr) {
                $dr['pareceres'] = $tbReadequacaoXParecer->buscarPareceresReadequacao(array('a.idReadequacao = ?'=>$dr['idReadequacao']))->toArray();
            }

            $resultArray = [];
            $resultArray['dadosReadequacoes'] = $dadosReadequacoes;
            $resultArray['dadosReadequacoesDevolvidas'] = $dadosReadequacoesDevolvidas;

            return $resultArray;
//            return $dadosReadequacoesDevolvidas;
//            $this->view->readequacoes = $dadosReadequacoes;
//            $this->view->readequacoesDevolvidas = $dadosReadequacoesDevolvidas;
        }
    }
}

