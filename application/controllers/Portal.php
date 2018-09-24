<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Carrega a Class Portal
 * 
 * Inicio do site onde aparecerá coisas importantes
 */
class Portal extends CI_Controller {
    
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
        
        $this->load->library('Adm_lib');
        $this->rodada_atual= $this->adm_lib->rodada_atual();
        $this->usuario_logado= $this->adm_lib->usuario_logado;
    }
    
    /**
     * Inicializa a pagina sem parametros.
     * 
     * @uses Portal::opcao()    Para carregar a view
     * @return void
     */
    public function index() {
        $this->opcao();
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
    public function opcao($onde=null){
        $msg= null;
        if($this->session->has_userdata('control_msg')){
            $msg= $this->session->control_msg;
            $this->session->unset_userdata('control_msg');
        }
        $dados = array(
            "usuario_logado"=> $this->usuario_logado,
            "msg" => $msg,
            "rodada_atual"=> $this->rodada_atual['rodada'],
            "existe_rodada"=> $this->rodada_atual['existe'],
            "detalhes_palpites"=> $this->dados_palpites(),
            "desafios"=> $this->todos_desafos()
        );
        
        $this->load->view('head', $dados);
        $this->load->view('portal');
    }
    
    /**
     * Tras os detalhes da rodada e os palpites resumidamente para mostrar ao usuario
     * 
     * @used-by Portal::opcao()                          Irá pegar os dados e mostrar no portal
     * @uses Portal::rodada_atual                        Mostrará somente a rodada atual
     * @uses Gerencia_model::consultar_rodada()          Tras os detalhes das partidas
     * @uses Palpites_model::palpites_usuario()          Tras os palpites
     * @return boolean
     */
    private function dados_palpites(){
        if(!$this->rodada_atual['rodada']){
            return false;
        }
        $this->load->model('Gerencia_model');
        $tras_detalhes_rodada= $this->Gerencia_model->consultar_rodada($this->rodada_atual['rodada']);
        $this->load->model('Palpites_model');
        $tras_palpites = $this->Palpites_model->palpites_usuario($this->usuario_logado['id'], $this->rodada_atual['rodada']);
        
        $dados= array(
            "rodada"=> $tras_detalhes_rodada,
            "palpites"=> $tras_palpites
        );
        
        return $dados;
    }
    
    /**
     * Irá trazer todos os desafios do usuário da rodada atual
     * 
     * @used-by Portal::opcao()                             Irá carregar os desafios e mostrar na view
     * @uses Desafios_model::todos_Desafios_rodada()        Busca os desafios da rodada
     * @uses Portal::rodada_atual               Rodada atual do bolao
     * @uses Portal::usuario_logado             Se existe um usuario logado
     * @return bool|array
     */
    public function todos_desafos(){
        $this->load->model('Desafios_model');
        $desafios= $this->Desafios_model->todos_desafios_rodada($this->rodada_atual['rodada'], $this->usuario_logado['id']);
        
        return $desafios;
    }

}
