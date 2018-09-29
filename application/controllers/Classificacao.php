<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Carrega a Class Portal
 * 
 * Inicio do site onde aparecerá coisas importantes
 */
class Classificacao extends CI_Controller {
    
    /**
     * O ID do usuario logado
     * 
     * @var int
     */
    private $usuario_logado;
    
    /**
     * Pega a rodada atual
     * 
     * @var int|bool
     */
    private $rodada_atual;
    
    /**
     * Carrega o Portal_model e Adm. Pega a rodada atual.
     * 
     * @uses Portal_model                         Carrega o Palpites_model para utilizar na aplicação
     * @uses Adm_lib::total_mangos_usuarios()     Tras a rodada atual
     * @return void
     */
    public function __construct() {
        parent::__construct();
        
        $this->load->model('Classificacao_model');
        $this->load->library('Adm_lib');
        $this->rodada_atual= $this->adm_lib->rodada_atual();
        $this->usuario_logado= $this->adm_lib->usuario_logado;
        $this->classif_geral();
    }
    
    /**
     * Inicializa a pagina sem parametros.
     * 
     * @uses Portal::opcao()    Para carregar a view
     * @return void
     */
    public function index() {
        $this->classificacao();
    }
    
    /**
     * Esse metodo irá montar a view passando parametros.
     * 
     * @uses Portal::dados_palpites()           Mostra as partidas e os palpites compactado no portal
     * @uses Portal::rodada_atual               Rodada atual do bolao
     * @uses Portal::usuario_logado             Se existe um usuario logado
     * @param String $onde                      Para identificar se retornou da copa ou do desafio
     * @param String $msg                       Uma palavra chave para mostrar a mensagem
     * @return void
     */
    public function classificacao($onde=null){
        $msg= null;
        if($this->session->has_userdata('control_msg')){
            $msg= $this->session->control_msg;
            $this->session->unset_userdata('control_msg');
        }
        $dados = array(
            "usuario_logado"=> $this->usuario_logado,
            "msg" => $msg,
        );
        
        $this->load->view('head', $dados);
        $this->load->view('classificacao');
    }
    
    public function classif_geral(){
        $rodada= ($this->rodada_atual['existe']) ? $this->rodada_atual['rodada']+1 : $this->rodada_atual['rodada']+1;
        $classif= $this->Classificacao_model->classif_geral($rodada);
        
        $this->load->model('User_model');
        $usuarios= $this->User_model->dados();
        
        foreach($usuarios AS $key=>$value){
            $usuarios[$key]['novo']= 1;
        }
        
        $this->load->model('Desafios_model');
        $desafios= $this->Desafios_model->teste($rodada);
        
        $this->load->model('Copa_model');
        $copa= $this->Copa_model->teste($rodada);
    }
}
