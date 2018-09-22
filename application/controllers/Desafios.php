<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Carrega a Class Desafios
 * 
 * Responsavel por cadastrar, calcular e atualizar os desafios
 */
class Desafios extends CI_Controller {
    
    /**
     * Pega a rodada atual
     * 
     * @var int|bool
     */
    public $rodada_atual;
    
    /**
     * Total de mangos em tempo real e da data atual.
     * 
     * @var float
     */
    public $mangos_total;
    
    /**
     * Carrega o Desafios_model e Adm. Pega a rodada atual.
     * 
     * @uses Desafios_model                         Carrega o Desafio_model para utilizar aqui
     * @uses Adm_lib::total_mangos_usuarios()       Tras a rodada atual e os mangos
     * @return void
     */
    public function __construct() {
        parent::__construct();
        
        $this->load->model('Desafios_model');
        $this->load->library('Adm_lib');
        $this->rodada_atual= $this->adm_lib->rodada_atual();
        $this->mangos_total= $this->adm_lib->total_mangos_usuario();
    }
    
    public function novo_desafio_individual(){
        $this->form_validation->set_rules("desafiado", "Desafiado", "trim|required|alpha_dash|min_length[2]|max_length[15]");
        if($this->form_validation->run()){
            if($this->mangos_total >= 1){
                $apelido= $this->input->post('desafiado');
                $verifica= $this->Desafios_model->novo_desafio_individual(1, $apelido);
                
                $msg= $verifica['msg'];
            } else{
                $erro= true;
                $msg= "Você não tem mangos suficiente. Diminua suas apostas nos palpites ou está praticamente falido :(";
            }
        } else{
            $erro= true;
            $msg= form_error('desafiado');
        }
        
        $this->session->set_userdata('control_msg', $msg);
        redirect(base_url("Portal/opcao/desafio_individual/"));
    }
}
