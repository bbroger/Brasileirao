<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Carrega a class Copa_model
 * 
 * Inscricoes, atualizacoes e dados da copa
 */
class Copa_model extends CI_Model {

    /**
     * Carregará a conexão com o banco
     * 
     * @var array
     */
    private $con;

    /**
     * Carrega o ConnectionFactory para poder utilizar o banco de dados
     * 
     * @uses ConnectionFactory::getConnection()     Irá pegar o objeto de conexão e salvar no Gerencia_model::con
     * @return void
     */
    public function __construct() {
        parent::__construct();

        $this->load->library('ConnectionFactory');
        $this->con = $this->connectionfactory->getConnection();
    }

    /**
     * Pega todas as participaçoes das copas oficiais e todos titulos se houver.
     * 
     * @used-by Adm_lib::todos_dados_usuarios()           Pega o total de participacoes e titulos para calcular os mangos
     * @uses Copa_model::total_titulos_oficial()          Pega todos os titulos oficiais das participacoes para ver quanto lucrou
     * @param int $id
     * @return array
     */
    public function total_dados_copa_oficial_user($id) {
        $sql = "SELECT COUNT(cac_oitavas) AS total FROM cac_cadastrar_copas WHERE cac_id_copa != ? AND cac_oitavas= ? AND YEAR(cac_created)= ?";

        $stmt = $this->con->prepare($sql);
        $stmt->bindValue(1, 1);
        $stmt->bindValue(2, $id);
        $stmt->bindValue(3, date('Y'));
        $stmt->execute();

        $dados_copa['total'] = $stmt->fetch(PDO::FETCH_ASSOC);
        $dados_copa['venceu']= $this->total_titulos_oficial($id);
        
        $stmt = null;
        return $dados_copa;
    }
    
    /**
     * Pega todos os titulos do usuario se existir.
     * 
     * @used-by Copa_model::total_dados_copa_oficial_user()           Pega todos os titulos para salvar junto com as participaçoes
     * @uses Copa_model::total_inscritos()                            Caso o usuário vença alguma coisa, ira pegar o total de participantes para somar e ver quanto lucrou
     * @param int $id
     * @return bool|array
     */
    public function total_titulos_oficial($id) {
        $sql = "SELECT cac_rodada, cac_id_copa FROM cac_cadastrar_copas WHERE cac_id_copa != ? AND cac_campeao= ? AND YEAR(cac_created)= ?";
        $stmt = $this->con->prepare($sql);
        $stmt->bindValue(1, 1);
        $stmt->bindValue(2, $id);
        $stmt->bindValue(3, date('Y'));
        $stmt->execute();

        $prov = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $dados_copa= false;
        if($prov){
            foreach ($prov as $key => $value) {
                $dados_copa[$key]['id_copa']= $value['cac_id_copa'];
                $dados_copa[$key]['rodada']= $value['cac_rodada'];
                $inscritos= $this->total_inscritos($value['cac_id_copa'], $value['cac_rodada']);
                $dados_copa[$key]['inscritos']= $inscritos['inscritos'];
            }
        }
        
        return $dados_copa;
    }
    
    /**
     * Tras o total de inscritos de uma copa
     * 
     * @used-by Copa_model::total_titulos()         Precisa ver o total de participantes para somar os mangos e ver o lucro do usuario
     * @param int   $id_copa                        Numero da copa para ver qual é a copa da rodada
     * @param int   $rodada                         Qual liga está na rodada
     * @param int   $id_liga                        OPCIONAL: Se o $id_copa for de liga, inserir o ID da liga
     * @return bool|array
     */
    public function total_inscritos($id_copa, $rodada, $id_liga= null){
        $sql = "SELECT COUNT(cac_oitavas) AS inscritos FROM cac_cadastrar_copas WHERE cac_id_copa= ? AND cac_rodada= ? AND YEAR(cac_created)= ?";
        $stmt = $this->con->prepare($sql);
        $stmt->bindValue(1, $id_copa);
        $stmt->bindValue(2, $rodada);
        $stmt->bindValue(3, date('Y'));
        $stmt->execute();
        
        $dados_copa= $stmt->fetch(PDO::FETCH_ASSOC);
        
        $stmt= null;
        return $dados_copa;
    }

}
