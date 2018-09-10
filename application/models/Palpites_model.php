<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Carrega a class Palpite_model
 * 
 * Depois que passou na Classe Palpites para aplicar a regra de negócio, aqui irá manipular no banco de dados.
 */
class Palpites_model extends CI_Model {

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
     * Irá trazer os palpites do usuário da rodada solicitada e do ano atual.
     * 
     * @param int $user_id          Irá selecionar os palpites do usuário informado
     * @param int $rodada           Irá selecionar os palpites da rodada informado
     * @return array|bool
     */
    public function palpites_usuario($user_id, $rodada) {
        $sql = "SELECT * FROM pap_palpites "
                . "WHERE pap_user_id= ? AND pap_rodada= ? AND pap_valida= 'sim' AND YEAR(pap_created) = " . date('Y') . " "
                . "ORDER BY pap_partida ASC LIMIT 10";
        $stmt = $this->con->prepare($sql);
        $stmt->bindValue(1, $user_id);
        $stmt->bindValue(2, $rodada);
        $stmt->execute();

        $tras_palpites = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt= null;
        return $tras_palpites;
    }
    
    /**
     * Irá trazer todos os palpites da rodada, partida e do ano atual para fazer os calculos dos palpites.
     * 
     * @used-by Gerencia::calcula_palpites()        Tras os palpites para fazer o calculo
     * @param int $rodada                           Para trazer a rodada solicitada
     * @param int $partida                          PAra trazere a partida solicitada
     * @return array|bool
     */
    public function todos_palpites_partidas($rodada, $partida) {
        $sql = "SELECT pap_id_palpite, pap_gol_mandante, pap_gol_visitante, pap_aposta FROM pap_palpites "
                . "WHERE pap_rodada= ? AND pap_partida= ? AND pap_palpitou = 'sim' AND pap_valida= 'sim' AND YEAR(pap_created) = " . date('Y');

        $stmt = $this->con->prepare($sql);
        $stmt->bindValue(1, $rodada);
        $stmt->bindValue(2, $partida);
        $stmt->execute();

        $tras_palpites = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt= null;
        return $tras_palpites;
    }
    
    /**
     * Irá salvar os palpites dos usuarios. Mesmo se o usuario for atualizar ja existente, irá salvar os novos palpites e desconsiderar os antigos.
     * 
     * @param int   $user_id          O ID do usuario para salvar na tabela pap_palpites
     * @param int   $rodada           Rodada para gravar a rodada que está palpitando
     * @param array $palpites         Esse array irá conter todos os palpites da rodada
     * @return void
     */
    public function salvar_palpites($user_id, $rodada, $palpites) {
        $this->invalidar_palpites_antigos($user_id, $rodada);
        $sql = "INSERT INTO pap_palpites (pap_user_id, pap_rodada, pap_partida, pap_gol_mandante, pap_gol_visitante, pap_aposta, pap_saldo, pap_palpitou) "
                . "VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->con->prepare($sql);
        foreach ($palpites as $key => $value) {
            $stmt->bindValue(1, $user_id);
            $stmt->bindValue(2, $rodada);
            $stmt->bindValue(3, $key);
            $stmt->bindValue(4, $value["gol_mandante"]);
            $stmt->bindValue(5, $value["gol_visitante"]);
            $stmt->bindValue(6, $value["aposta"]);
            $stmt->bindValue(7, $value["saldo"]);
            $stmt->bindValue(8, $value["palpitou"]);
            $stmt->execute();
            $stmt= null;
        }
        $stmt= null;
    }
    
    private function invalidar_palpites_antigos($user_id, $rodada){
        $sql= "UPDATE pap_palpites SET pap_valida = 'nao' WHERE pap_user_id = ? AND pap_rodada= ? AND YEAR(pap_created) = " . date('Y');
        $stmt= $this->con->prepare($sql);
        $stmt->bindValue(1, $user_id);
        $stmt->bindValue(2, $rodada);
        $stmt->execute();
        $stmt= null;
    }

}
