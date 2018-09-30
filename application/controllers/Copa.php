<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Carrega a Class Copa
 * 
 * Aqui irá fazer novos cadastros, atualizaçoes e consultar copas
 */
class Copa extends CI_Controller {
    
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
     * Carrega o Copa_model e Adm. Pega a rodada atual.
     * 
     * @uses Copa_model                           Carrega o Copa_model para utilizar na aplicação
     * @uses Adm_lib::total_mangos_usuarios()     Tras a rodada atual
     * @return void
     */
    public function __construct() {
        parent::__construct();
        
        $this->load->library('Adm_lib');
        $this->rodada_atual= $this->adm_lib->rodada_atual();
        $this->usuario_logado= $this->adm_lib->usuario_logado;
    }
    
    /**
     * Irá receber uma mensagem e redirecionar para o portal
     * 
     * @param String $msg
     * @return void
     */
    private function control_msg($msg) {
        $this->session->set_userdata('control_msg', $msg);
        redirect(base_url("Portal/opcao/copa/"));
    }
    
    /**
     * Inicializa a pagina sem parametros.
     * 
     * @uses Copa::opcao()    Para carregar a view
     * @return void
     */
    public function index() {
        $this->opcao();
    }
    
    /**
     * Esse metodo irá montar a view passando parametros.
     * 
     * @uses Portal::rodada_atual               Rodada atual do bolao
     * @uses Portal::usuario_logado             Se existe um usuario logado
     * @param String $msg                       Uma palavra chave para mostrar a mensagem
     * @return void
     */
    public function opcao($msg=null){
        $dados = array(
            "usuario_logado"=> $this->usuario_logado,
            "msg" => $msg,
            "rodada_atual"=> $this->rodada_atual['rodada']
        );
        
        $this->load->view('head', $dados);
        $this->load->view('copa');
    }
    
    public function inscricao_copa(){
        
    }
}
