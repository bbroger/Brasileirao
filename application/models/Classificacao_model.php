<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Carrega a class Classificacao_model
 * 
 * Irá trazer as classificacoes do bolao, os aptos para campeonatos, pontuacoes e saldos dos usuarios
 */
class Classificacao_model extends CI_Model {

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
     * Vai trazer o total dos ganhos na classificaçao do bolao como pontos lucro, cc, ct e cf.
     * 
     * @used-by Adm_lib()         Irá trazer total dos ganhos na classificaçao e somara com os demais ganhos.
     * @param int $id             ID do usuario para consultar
     * @return bool|array
     */
    public function total_consulta_classif_user($id){
        $sql= "SELECT SUM(pap_cc) AS cc, SUM(pap_ct) AS ct, SUM(pap_cf) AS cf, SUM(pap_pontos) AS pontos, SUM(pap_saldo) AS total_saldo FROM pap_palpites "
                . "WHERE pap_user_id= ? AND pap_valida= 'sim' AND YEAR(pap_created) = ? GROUP BY pap_user_id";
        $stmt= $this->con->prepare($sql);
        $stmt->bindValue(1, $id);
        $stmt->bindValue(2, date('Y'));
        $stmt->execute();
        
        $dados_classif= $stmt->fetch(PDO::FETCH_ASSOC);
        
        $stmt= null;
        return $dados_classif;
    }
    
    public function classif_geral($rodada){
        $sql= "SELECT user.use_id_user AS id, user.use_nickname AS apelido, user.use_img_perfil AS img_perfil, user.use_mangos AS mangos, "
                . "SUM(pap.pap_cc) AS cc, SUM(pap.pap_ct) AS ct, SUM(pap.pap_cf) AS cf, SUM(pap.pap_pontos) AS pontos, SUM(pap.pap_saldo) AS saldo FROM use_users AS user "
                . "LEFT JOIN pap_palpites AS pap ON user.use_id_user = pap.pap_user_id "
                . "WHERE pap.pap_rodada < ? AND pap.pap_valida= 'sim' AND YEAR(pap.pap_created)= ? GROUP BY user.use_id_user";
        $stmt= $this->con->prepare($sql);
        $stmt->bindValue(1, $rodada);
        $stmt->bindValue(2, date('Y'));
        $stmt->execute();
        
        $dados_classif= $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $stmt= null;
        return $dados_classif;
    }
}
