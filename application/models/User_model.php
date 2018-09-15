<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Carrega a class User_model
 * 
 * Ira cadastrar e pegar todos os dados do Usuario como total de mangos, pontos, titulos, etc...
 */
class User_model extends CI_Model {

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
     * Irá trazer os dados básicos do usuario
     * 
     * @used-by Adm_lib         Irá trazer tudo sobre usuario começando com os dados
     * @param int $id           Trazer um usuario especifico
     * @return array
     */
    public function dados($id){
        $sql="SELECT use_nickname, use_name, use_img_perfil, use_type, use_mangos, use_type, use_created FROM use_users WHERE use_id_user = ?";
        $stmt= $this->con->prepare($sql);
        $stmt->bindValue(1, $id);
        $stmt->execute();
        
        $dados_user= $stmt->fetch(PDO::FETCH_ASSOC);
        
        $stmt= null;
        return $dados_user;
    }
}
