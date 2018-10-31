<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Carrega a class Liga_model
 * 
 * Nova liga ou consultar elas
 */
class Liga_model extends CI_Model {

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
     * Verifica se o ID da liga informado existe
     * 
     * @param type $id_liga         ID da liga
     * @return bool
     */
    public function verifica_liga($id_liga){
        $sql= "SELECT lig_id_liga FROM lig_ligas WHERE lig_id_liga = ? AND lig_status= ?";
        $stmt= $this->con->prepare($sql);
        $stmt->bindValue(1, $id_liga);
        $stmt->bindValue(2, "ativo");
        $stmt->execute();
        
        $valida= $stmt->fetch();
        $stmt= null;
        
        return $valida;
    }
    
    /**
     * Verifica se um usuario participa de uma liga
     * 
     * @param type $id_liga         ID liga
     * @param type $id_usuario      Usuario
     * @return type
     */
    public function verifica_user_liga($id_liga, $id_usuario){
        $sql= "SELECT mel_id_inscricao FROM mel_membros_ligas WHERE mel_id_liga = ? AND mel_id_user= ? AND mel_status= ?";
        $stmt= $this->con->prepare($sql);
        $stmt->bindValue(1, $id_liga);
        $stmt->bindValue(2, $id_usuario);
        $stmt->bindValue(3, "ativo");
        $stmt->execute();
        
        $valida= $stmt->fetch();
        $stmt= null;
        
        return $valida;
    }
}
