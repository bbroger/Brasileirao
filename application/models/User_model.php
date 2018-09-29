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
     * @param int $id           Trazer um usuario especifico. Se tiver em branco, trará todos usuarios
     * @return array
     */
    public function dados($id = null) {
        if ($id) {
            $sql = "SELECT * FROM use_users WHERE use_id_user = ?";
        } else {
            $sql = "SELECT * FROM use_users";
        }
        $stmt = $this->con->prepare($sql);
        if ($id) {
            $stmt->bindValue(1, $id);
            $stmt->execute();
            $dados_user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($dados_user) {
                $dados_user['use_img_perfil'] = base_url("assets/images/perfil/" . $dados_user['use_img_perfil']);
            }
        } else{
            $stmt->execute();
            $dados_user = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        $stmt = null;
        return $dados_user;
    }

}
