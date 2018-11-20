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
     * @used-by Adm_lib::todos_dados_usuarios         Irá trazer total dos ganhos na classificaçao e somara com os demais ganhos.
     * @param int $id                                 ID do usuario para consultar
     * @return array
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
    
    /**
     * Trás o total dos ganhos nos palpites do bolao como pontos, lucro, cc... A consulta trará tudo da rodada 1 até a rodada atual -1
     * 
     * @used-by Adm_lib::classif_geral()                Pega os palpites dos usuarios para somar na classificacao
     * @param int $rodada                               Rodada atual ou a rodada anterior da total. Se nao existir, virá como 0.
     * @return array
     */
    public function classif_geral($rodada){
        $sql= "SELECT user.use_id_user AS id, "
                . "SUM(pap.pap_cc) AS cc, SUM(pap.pap_ct) AS ct, SUM(pap.pap_cf) AS cf, SUM(pap.pap_pontos) AS pontos, SUM(pap.pap_saldo) AS saldo FROM use_users AS user "
                . "LEFT JOIN pap_palpites AS pap ON user.use_id_user = pap.pap_user_id "
                . "WHERE pap.pap_rodada < ? AND pap.pap_valida= 'sim' AND YEAR(pap.pap_created)= ? GROUP BY user.use_id_user";
        $stmt= $this->con->prepare($sql);
        $stmt->bindValue(1, $rodada);
        $stmt->bindValue(2, date('Y'));
        $stmt->execute();
        
        $dados= $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt= null;
        
        if(!$dados){
            return array();
        }
        
        foreach ($dados AS $key => $value) {
            $dados_classif[$value['id']]['cc']= $value['cc'];
            $dados_classif[$value['id']]['ct']= $value['ct'];
            $dados_classif[$value['id']]['cf']= $value['cf'];
            $dados_classif[$value['id']]['pontos']= $value['pontos'];
            $dados_classif[$value['id']]['saldo']= $value['saldo'];
        }
        
        return $dados_classif;
    }
    
    /**
     * Irá pegar a solicitaçao da rodada desejado. Somentes partidas validas e que nao foram adiados
     * 
     * @used-by Copa_model::tras_partic()           Depois que consultou os participantes, pega a pontuaçao de todos
     * @used-by Desafios_model::pega_adversarios()  Pega a pontuaçao da rodada informado para mostrar nos desafios
     * @param type $id_usuario
     * @param type $rodada
     * @return array
     */
    public function consulta_classif_user_rodada($id_usuario, $rodada){
        $sql= "SELECT SUM(pap_cc) AS cc, SUM(pap_ct) AS ct, SUM(pap_cf) AS cf, SUM(pap_pontos) AS pontos, SUM(pap_saldo) AS saldo FROM pap_palpites "
                . "WHERE pap_user_id= ? AND pap_rodada= ? AND pap_adiou= ? AND pap_palpitou= ? AND pap_valida= ? AND YEAR(pap_created)= ?";
        $stmt= $this->con->prepare($sql);
        $stmt->bindValue(1, $id_usuario);
        $stmt->bindValue(2, $rodada);
        $stmt->bindValue(3, 'nao');
        $stmt->bindValue(4, 'sim');
        $stmt->bindValue(5, 'sim');
        $stmt->bindValue(6, date('Y'));
        $stmt->execute();
        
        $pega= $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt= null;
        
        $pega['cc']+=0;
        $pega['ct']+=0;
        $pega['cf']+=0;
        $pega['pontos']+=0;
        $pega['saldo']+=0;
        
        return $pega;
    }
}
