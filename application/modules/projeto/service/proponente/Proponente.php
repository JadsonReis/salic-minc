<?php

namespace Application\Modules\Projeto\Service\Proponente;

use Seguranca;
use ConsultarDadosProjetoDAO;
use ProponenteDAO;
use Projetos;
use Agente_Model_DbTable_Agentes;
use tbProcuradorProjeto;
use Proposta_Model_DbTable_PreProjeto;
use tbAgentesxVerificacao;
use TratarArray;

class Proponente
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

    public function buscarDadosAgenteProponente()
    {
        $parametros = $this->request->getParams();
        $proponente = [];

        try {

            $idPronac = $parametros['idPronac'];
            if (strlen($idPronac) > 7) {
                $idPronac = \Seguranca::dencrypt($idPronac);
            }

            if (empty($idPronac)) {
                throw new \Exception("idPronac &eacute; obrigat&oacute;rio");
            }

            $dbTableProjetos = new \Projeto_Model_DbTable_Projetos();
            $projeto = $dbTableProjetos->findBy(['IdPronac = ?' => $idPronac]);

            if (empty($projeto)) {
                throw new \Exception("Nenhum projeto encontrado!");
            }

            $proponenteDAO = new \ProponenteDAO();

            $dadosProponente = $proponenteDAO->execPaProponente($idPronac, \Zend_DB::FETCH_ASSOC);
            $proponente['dados'] = $dadosProponente[0];

            $idAgente = $dadosProponente[0]['idAgente'];

            $dbTableInabilitado = new \Inabilitado();
            $proponenteInabilitado = $dbTableInabilitado->BuscarInabilitado($projeto["CgcCpf"], null, null, true);

            $proponente['proponenteInabilitado'] = !empty($proponenteInabilitado);

            $dbTableInternet = new \Agente_Model_DbTable_Internet();
            $proponente['emails'] = $dbTableInternet->buscarEmails($idAgente)->toArray();

            $dbTableTelefones = new \Agente_Model_DbTable_Telefones();
            $proponente['telefones'] = $dbTableTelefones->buscarFones($idAgente)->toArray();

            $dbTableEndereco = new \Agente_Model_DbTable_EnderecoNacional();
            $proponente['enderecos'] = $dbTableEndereco->buscarEnderecos($idAgente)->toArray();

            $tbProcuradorProjeto = new \tbProcuradorProjeto();
            $proponente['procuradores'] = $tbProcuradorProjeto->buscarProcuradorDoProjeto($idPronac)->toArray();


            $dbTableAgente = new \Agente_Model_DbTable_Agentes();
            $dirigentes = $dbTableAgente->buscarDirigentes(
                ['v.idVinculoPrincipal = ?' => $idAgente, 'n.Status =?' => 0],
                ['n.Descricao ASC']
            )->toArray();

            $arrDirigentes = [];
            if (!empty($projeto["idProjeto"])) {
                $dbTablePreProjeto = new \Proposta_Model_DbTable_PreProjeto();
                $preProjeto = $dbTablePreProjeto->findBy(['idPreProjeto = ?' => $projeto["idProjeto"]]);
                $tbDirigenteMandato = new \tbAgentesxVerificacao();
                foreach ($dirigentes as $dirigente) {
                    $rsMandato = $tbDirigenteMandato->listarMandato([
                        'idEmpresa = ?' => $preProjeto['idAgente'],
                        'idDirigente = ?' => $dirigente['idAgente'],
                        'stMandato = ?' => 0]
                    )->toArray();
                    $dirigente['mandatos'] = $rsMandato;
                    $arrDirigentes[] = $dirigente;
                }
            }

            $proponente['dirigentes'] = $arrDirigentes;

            $proponente = \TratarArray::utf8EncodeArray($proponente);

            return $proponente;

        } catch (\Exception $objException) {
            throw $objException;
        }
    }
}
