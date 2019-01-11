<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Carrega a Class Liga
 * 
 * Aqui irá criar ou procurar ligas
 */
class Liga extends CI_Controller {
    
    /**
     * O ID do usuario logado
     * 
     * @var array
     */
    private $usuario_logado;
    
    /**
     * Trás as ligas pelo Adm_lib
     * 
     * @var array 
     */
    private $ligas;
    
    /**
     * Pega a rodada atual
     * 
     * @var int
     */
    private $rodada_atual;

    /**
     * Carrega o Copa_model, Liga_model e Adm. Pega a rodada atual.
     * 
     * @uses Liga_model                           Carrega a Liga_model para utilizar na aplicação
     * @uses Adm_lib::rodada_atual()              Nao pode cadastrar em copas se nao tiver rodada.
     * @uses Adm_lib::todos_dados_usuarios()      Recebe todas as copas do usuário
     * @return void
     */
    public function __construct() {
        parent::__construct();

        $this->load->model('Liga_model');
        $this->load->library('Adm_lib');
        
        $this->rodada_atual = $this->adm_lib->rodada_atual();
        $this->usuario_logado = $this->adm_lib->usuario_logado;
        $this->ligas= $this->adm_lib->todos_dados_usuarios($this->usuario_logado['id'], array('ligas'));
    }
    
    /**
     * Inicializa a pagina sem parametros.
     * 
     * @uses Liga::ligas    Leva as ligas para view
     * @return void
     */
    public function index($msg= null){
        $dados = array(
            "msg" => $msg,
            "ligas"=> $this->ligas['ligas']
        );
        
        $this->load->view('head', $dados);
        $this->load->view('liga');
    }
    
    public function cadastrar_ligas(){
        $this->form_validation->set_rules("nome", "<strong>Nome</strong>", "trim|required|min_length[4]|max_length[15]|callback_verifica_nome");
        
        if(!$this->form_validation->run()){
            $this->index();
        } else{
        }
    }
    
    public function verifica_nome($nome){
        $this->form_validation->set_message("verifica_nome", "O campo <b>CPF</b> é obrigatório.");
        return true;
    }
}
