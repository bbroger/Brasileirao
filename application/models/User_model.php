<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Carrega a class User_model
 * 
 * Pegará todos os dados do Usuario como total de mangos, pontos, titulos, etc...
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

}
