<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Palpites_model extends CI_Model {

    private $con;

    public function __construct() {
        parent::__construct();
        
        $this->load->library('ConnectionFactory');
        $this->con = $this->connectionfactory->getConnection();
        $this->palpites_usuario(1, 1);
    }
    
    public function palpites_usuario($user_id, $rodada){
        $sql= "SELECT pap.*, cad.* FROM pap_palpites AS pap "
                . "INNER JOIN cad_cadastrar_rodadas AS cad ON pap.pap_rodada = cad.cad_rodada AND pap.pap_partida = cad.cad_partida "
                . "WHERE cad.cad_rodada = ?";
        $stmt= $this->con->prepare($sql);
        $stmt->bindValue(1, $rodada);
        $stmt->execute();
        
        $tras_tabela= $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $tras_tabela;
    }
    
    public function todos_palpites_partidas($rodada, $partida){
        $sql= "SELECT * FROM pap_palpites WHERE pap_rodada= ? AND pap_partida= ? AND pap_palpitou = 'sim'";
        
        $stmt= $this->con->prepare($sql);
        $stmt->bindValue(1, $rodada);
        $stmt->bindValue(2, $partida);
        $stmt->execute();
        
        $tras_palpites= $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $tras_palpites;
    }

    public function salvar_palpites($rodada, $palpites){
        $sql= "UPDATE pap_palpites SET pap_gol_mandante= ?, pap_gol_visitante= ?, pap_aposta= ?, pap_palpitou= 'sim' WHERE pap_user_id= ? AND pap_rodada= ? AND pap_partida= ?";
        $stmt= $this->con->prepare($sql);
        foreach ($palpites as $key => $value) {
            $stmt->bindValue(1, $value["mandante"]);
            $stmt->bindValue(2, $value["visitante"]);
            $stmt->bindValue(3, $value["aposta"]);
            $stmt->bindValue(4, 1);
            $stmt->bindValue(5, $rodada);
            $stmt->bindValue(6, $key);
            $stmt->execute();
        }
    }
    
    private function inserir_palpites($id_usuario= 1){
        $sql= "INSERT INTO pap_palpites (pap_user_id, pap_rodada, pap_partida) VALUES(?, ?, ?)";
        $stmt= $this->con->prepare($sql);
        for($i= 1; $i <= 38; $i++){
            for($ii= 1; $ii <= 10; $ii++){
                $stmt->bindValue(1, $id_usuario);
                $stmt->bindValue(2, $i);
                $stmt->bindValue(3, $ii);
                $stmt->execute() or die($stmt->errorInfo());
            }
        }
    }
}
