<?php

/**
 * View para painel de avaliação das propostas e tranformação em projetos.
 *
 * @link http://salic.cultura.gov.br
 */
class Admissibilidade_Model_DbTable_VwPainelAvaliarPropostas extends MinC_Db_Table_Abstract
{
    protected $_schema = 'sac';
    protected $_name = 'vwPainelAvaliarPropostas';
    protected $_primary = 'idProjeto';

    public function propostas($where = array(), $order = array(), $start = 0, $limit = 10, $search = null)
    {
        $db = $this->getAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        $sql = $db->select()
            ->from('vwPainelAvaliarPropostas', '*', $this->_schema);

        foreach ($where as $coluna => $valor) {
            $sql->where($coluna, $valor);
        }

        if (!empty($search['value'])) {
            $sql->where('idProjeto like ? OR NomeProposta like ? OR Tecnico like ?', '%' . $search['value'] . '%');
        }

        $sql->order($order);

        if (!is_null($start) && $limit) {
            $start = (int)$start;
            $limit = (int)$limit;
            $sql->limitPage($start, $limit);
        }

        return $db->fetchAll($sql);
    }

    public function obterPropostasParaAvaliacao(
        $where = [],
        $order = [],
        $start = 0,
        $limit = 10,
        $search = null,
        Admissibilidade_Model_DistribuicaoAvaliacaoProposta $distribuicaoAvaliacaoProposta = null
    )
    {
        $db = $this->getAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from('vwPainelAvaliarPropostas',
            ['*'],
            $this->_schema);

        if (!is_null($start) && $limit) {
            $start = (int)$start;
            $limit = (int)$limit;
            $select->limitPage($start, $limit);
        }

        $sqlPerfisDistribuicao = '';
        $perfisDistribuicao = $this->obterPerfisDistribuicao($distribuicaoAvaliacaoProposta);
        if ($perfisDistribuicao) {
            $sqlPerfisDistribuicao = " and distribuicao_avaliacao_proposta.id_perfil in ({$perfisDistribuicao}) ";
        }

        $select->joinLeft(
            ['distribuicao_avaliacao_proposta']
            , "distribuicao_avaliacao_proposta.id_preprojeto = vwPainelAvaliarPropostas.idProjeto
                    {$sqlPerfisDistribuicao}
                    and distribuicao_avaliacao_proposta.id_orgao_superior = {$distribuicaoAvaliacaoProposta->getIdOrgaoSuperior()}"
            ,
            [
                'avaliacao_atual' => "coalesce(distribuicao_avaliacao_proposta.avaliacao_atual, '0')",
                'quantidade_distribuicoes' => "coalesce(distribuicao_avaliacao_proposta.id_distribuicao_avaliacao_proposta, '0')"
            ]
            , $this->getSchema('sac')
        );

        if ($distribuicaoAvaliacaoProposta->getIdPerfil() == Autenticacao_Model_Grupos::COORDENADOR_ADMISSIBILIDADE) {
            $select->joinLeft(
                ['sugestao_enquadramento']
                , "
                        sugestao_enquadramento.id_preprojeto = distribuicao_avaliacao_proposta.id_preprojeto
                        and sugestao_enquadramento.id_orgao = {$distribuicaoAvaliacaoProposta->getIdOrgaoSuperior()}
                        and sugestao_enquadramento.id_perfil_usuario = {$distribuicaoAvaliacaoProposta->getIdPerfil()}
                    "
                , ['sugestao_enquadramento.id_area']
                , $this->getSchema('sac')
            );
        }

        foreach ($where as $coluna => $valor) {
            $select->where($coluna, $valor);
        }

        if (!empty($search['value'])) {
            $select->where('idProjeto like ? OR NomeProposta like ? OR Tecnico like ?', "%{$search['value']}%");
        }

        $restricaoPropostasParaAvaliacao = $this->obterRestricaoPropostasParaAvaliacao($distribuicaoAvaliacaoProposta);
        if($restricaoPropostasParaAvaliacao) {
            $select->where($restricaoPropostasParaAvaliacao);
        }

        if ($order) {
            $select->order($order);
        }

        //xdnb($select->assemble());
        return $db->fetchAll($select);
    }

    private function obterRestricaoPropostasParaAvaliacao(Admissibilidade_Model_DistribuicaoAvaliacaoProposta $distribuicaoAvaliacaoProposta)
    {
        if($distribuicaoAvaliacaoProposta->getIdPerfil()) {
            $restricaoPropostasParaAvaliacao = '( ';
            if ($distribuicaoAvaliacaoProposta->getIdPerfil() == Autenticacao_Model_Grupos::TECNICO_ADMISSIBILIDADE
                || $distribuicaoAvaliacaoProposta->getIdPerfil() == Autenticacao_Model_Grupos::COORDENADOR_ADMISSIBILIDADE) {
                $restricaoPropostasParaAvaliacao .= ' distribuicao_avaliacao_proposta.avaliacao_atual is null ';
                $restricaoPropostasParaAvaliacao .= ' AND distribuicao_avaliacao_proposta.id_distribuicao_avaliacao_proposta is null ';
            }
            if ($distribuicaoAvaliacaoProposta->getIdPerfil() != Autenticacao_Model_Grupos::TECNICO_ADMISSIBILIDADE) {
                if (!empty($restricaoPropostasParaAvaliacao)) {
                    $restricaoPropostasParaAvaliacao .= ' OR ';
                }
                $restricaoPropostasParaAvaliacao .= 'distribuicao_avaliacao_proposta.avaliacao_atual = 1 and distribuicao_avaliacao_proposta.id_distribuicao_avaliacao_proposta > 0';
            }
            $restricaoPropostasParaAvaliacao .= ' )';
            return $restricaoPropostasParaAvaliacao;
        }
    }


    private function obterPerfisDistribuicao(Admissibilidade_Model_DistribuicaoAvaliacaoProposta $distribuicaoAvaliacaoProposta)
    {
        if ($distribuicaoAvaliacaoProposta->getIdPerfil() != Autenticacao_Model_Grupos::TECNICO_ADMISSIBILIDADE) {
            $perfis = [
                $distribuicaoAvaliacaoProposta->getIdPerfil()
            ];

            if ($distribuicaoAvaliacaoProposta->getIdPerfil() == Autenticacao_Model_Grupos::COORDENADOR_ADMISSIBILIDADE) {
                $perfis[] = Autenticacao_Model_Grupos::TECNICO_ADMISSIBILIDADE;
            }
            return implode(',', $perfis);
        }
    }

    public function propostasTotal($where = array(), $order = array(), $start = null, $limit = null, $search = null)
    {
        $db = $this->getAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        $sql = $db->select()
            ->from('vwPainelAvaliarPropostas', 'count(*) as total', $this->_schema);

        foreach ($where as $coluna => $valor) {
            $sql->where($coluna, $valor);
        }

        if (!empty($search['value'])) {
            $sql->where('idProjeto like ? OR NomeProposta like ? OR Tecnico like ?', '%' . $search['value'] . '%');
        }

        $sql->order($order);

        if (!is_null($start) && $limit) {
            $start = (int)$start;
            $limit = (int)$limit;
            $sql->limitPage($start, $limit);
        }
        //echo $sql;

        return $db->fetchRow($sql);
    }
}
