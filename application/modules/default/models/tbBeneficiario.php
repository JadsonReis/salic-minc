<?php

class tbBeneficiario extends MinC_Db_Table_Abstract
{
    protected $_schema = "SAC";
    protected $_name   = "tbBeneficiario";

    /**
     * @access public
     * @param array $dados
     * @return integer (retorna o último id cadastrado)
     */
    public function cadastrarDados($dados)
    {
        return $this->insert($dados);
    }


    /**
     * @access public
     * @param array $dados
     * @param integer $where
     * @return integer (quantidade de registros alterados)
     */
    public function alterarDados($dados, $where)
    {
        $where = "idBeneficiario = " . $where;
        return $this->update($dados, $where);
    }

    public function buscarUsandoCAST($idRelatorio)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                    array('a' => $this->_name),
                    array('a.idRelatorio', new Zend_Db_Expr('CAST(a.dsBeneficiario AS TEXT) AS dsBeneficiario'), 'a.tpBeneficiario', 'tpBeneficiario', 'a.nrCNPJ', 'a.nrCPF', 'CAST(a.dsPublicoAlvo AS TEXT) AS dsPublicoAlvo', 'CAST(a.dsEntrega AS TEXT) AS dsEntrega')
            );
        $select->where('a.idRelatorio = ?', $idRelatorio);
    
        return $this->fetchAll($select);
    }

    public function buscarJustificativaCAST($idRelatorio)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                    array('a' => $this->_name),
                    array(
                            'a.idRelatorio',
                            'a.nrCNPJ',
                            'a.nrCPF',
                            'a.dsBeneficiario',
                        new Zend_Db_Expr('CAST(a.dsPublicoAlvo AS TEXT) AS dsPublicoAlvo'),
                            new Zend_Db_Expr('CAST(a.dsEntrega AS TEXT) AS dsEntrega'),
                            'a.tpBeneficiario',
                            'a.stCNPJ',
                            'a.stCPF',
                            'a.stPublicoAlvo',
                                new Zend_Db_Expr('CAST(a.dsJustificativaAcompanhamento AS TEXT) AS dsJustificativaAcompanhamento'),
                        )
            );
        $select->where('a.idRelatorio = ?', $idRelatorio);
    
        return $this->fetchAll($select);
    }
}
