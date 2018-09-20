<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Carrega a Class Portal
 * 
 * Inicio do site onde aparecerá coisas importantes
 */
class Portal extends CI_Controller {
    
    /**
     * Pega a rodada atual
     * 
     * @var int|bool
     */
    public $rodada_atual;
    
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
     * @param String $onde                      Para identificar se retornou da copa ou do desafio
     * @param String $msg                       Uma palavra chave para mostrar a mensagem
     * @return void
     */
    public function opcao($onde=null, $msg= null){
        $dados = array(
            "msg" => null,
            "rodada_atual"=> $this->rodada_atual,
            "detalhes_palpites"=> $this->dados_palpites()
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
        if(!$this->rodada_atual){
            return false;
        }
        $this->load->model('Gerencia_model');
        $tras_detalhes_rodada= $this->Gerencia_model->consultar_rodada($this->rodada_atual);
        $this->load->model('Palpites_model');
        $tras_palpites = $this->Palpites_model->palpites_usuario(1, $this->rodada_atual);
        
        $dados= array(
            "rodada"=> $tras_detalhes_rodada,
            "palpites"=> $tras_palpites
        );
        
        return $dados;
    }

}
