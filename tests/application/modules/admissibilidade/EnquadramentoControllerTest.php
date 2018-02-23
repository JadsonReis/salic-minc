<?php

class EnquadramentoControllerTest extends MinC_Test_ControllerActionTestCase
{

    /**
     * TestEncaminharAssinatura Listagem de projetos disponíveis para encaminhar para assinatura
     *
     * @access public
     * @return void
     */
    public function testEncaminharAssinatura()
    {
        $this->autenticar();

        $this->alterarPerfil(Autenticacao_Model_Grupos::COORDENADOR_GERAL_ADMISSIBILIDADE, Orgaos::ORGAO_SEFIC_DIC);

        //reset para garantir respostas.
        $this->resetRequest()
            ->resetResponse();

        $this->dispatch('/admissibilidade/enquadramento/encaminhar-assinatura');
        $this->assertModule('admissibilidade');
        $this->assertController('enquadramento');
        $this->assertAction('encaminhar-assinatura');
    }

    /**
     * TestEncaminharAssinatura Listagem de assinaturas disponíveis
     *
     * @access public
     * @return void
     */
    public function testAssinatura()
    {
        $this->autenticar();

        $this->alterarPerfil(Autenticacao_Model_Grupos::COORDENADOR_GERAL_ADMISSIBILIDADE, Orgaos::ORGAO_SEFIC_DIC);

        //reset para garantir respostas.
        $this->resetRequest()
            ->resetResponse();

        $this->dispatch('/admissibilidade/enquadramento-assinatura/gerenciar-assinaturas');
        $this->assertModule('admissibilidade');
        $this->assertController('enquadramento-assinatura');
        $this->assertAction('gerenciar-assinaturas');
    }
}
