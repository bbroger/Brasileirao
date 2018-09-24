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
    private $rodada_atual;

    /**
     * O ID do usuario logado
     * 
     * @var int
     */
    private $usuario_logado;

    /**
     * Total de mangos em tempo real e da data atual.
     * 
     * @var float
     */
    private $mangos_total;

    /**
     * Carrega o Desafios_model e Adm. Pega a rodada atual e os dados do usuario logado.
     * 
     * @uses Desafios_model                         Carrega o Desafio_model para utilizar aqui
     * @uses Adm_lib::total_mangos_usuarios()       Tras a rodada atual e os mangos
     * @return void
     */
    public function __construct() {
        parent::__construct();

        $this->load->model('Desafios_model');
        $this->load->library('Adm_lib');
        $this->rodada_atual = $this->adm_lib->rodada_atual();
        $this->usuario_logado = $this->adm_lib->usuario_logado;
        $this->mangos_total = $this->adm_lib->total_mangos_usuario($this->usuario_logado['id']);


        if (!$this->usuario_logado['logado']) {
            redirect(base_url("Portal/"));
        }
    }

    /**
     * Irá receber uma mensagem e redirecionar para o portal
     * 
     * @used-by Desafios::novo_desafio_individual()         Se houver erros ou sucesso, irá redirecionar
     * @used-by Desafios::decisao_desafio()                 Se houver erros ou sucesso, irá redirecionar
     * @param String $msg
     * @return void
     */
    private function control_msg($msg) {
        $this->session->set_userdata('control_msg', $msg);
        redirect(base_url("Portal/opcao/desafio_individual/"));
    }

    /**
     * Quando o usuário for desafiado, ele irá aceitar ou recusar.
     * 
     * @uses Desafios_model::tras_id_adversario()                Irá verificar se existe o adversário. Se sim, retorna o ID
     * @uses Desafios_model::verifica_existencia_desafio()       Irá verificar se existe o desafio pendente. Se nao existir, não tem o que decidir.
     * @uses Desafios_model::decisao_desafio()                   Irá mandar os dados dos desafiantes mais a decisão para ser atualizado no banco.
     * @uses Desafios::usuario_logado                            Visitantes nao podem desafiar, entao confere se tem alguem logado
     * @uses Desafios::rodada_atual                              Só pode palpitar se existir rodada para isso
     * @uses Desafios::control_msg()                             Redireciona para o portal com uma mensagem.
     * @return void
     */
    public function decisao_desafio() {
        if (!$this->rodada_atual['existe']) {

            $msg = "Não existe rodada cadastrada no momento. Por favor aguarde ser cadastrado.";
            $this->control_msg($msg);
        }

        $hoje = new DateTime();
        $data_inicio = new DateTime($this->rodada_atual['inicio']);

        if ($hoje > $data_inicio) {

            $msg = "A rodada começou " . $data_inicio->format('d/m H:i') . ", não dá mais tempo de fazer nada :'(. "
                    . "Não se preocupe, no final da rodada irá sumir esse desafio e se tiver que devolver algum valor, será devolvido";
            $this->control_msg($msg);
        }

        $this->form_validation->set_rules("decisao", "<strong>Decisão</strong>", "trim|required|in_list[novo,aceito,recusado,cancelado]");
        $this->form_validation->set_rules("adversario", "<b>Adversário</b>", "trim|required|alpha_dash|min_length[2]|max_length[15]");

        if ($this->form_validation->run()) {
            $decisao = strtolower($this->input->post('decisao'));
            $apelido = $this->input->post('adversario');

            if (($decisao == "novo" || $decisao == "aceito") && $this->mangos_total < 1) {
                $msg = "Você não tem mangos suficiente. Diminua suas apostas nos palpites ou está praticamente falido :(";
                $this->control_msg($msg);
            }
        } else {
            $msg = validation_errors();
            $this->control_msg($msg);
        }
        
        $tras_id_adversario = $this->Desafios_model->tras_id_adversario($apelido);
        if (!$tras_id_adversario) {
            $msg = "Esse usuário $apelido não foi encontrado :S. Informe corretamenteo adversário ou nada será feito!";
            $this->control_msg($msg);
        }
        $id_adversario= $tras_id_adversario['use_id_user'];
        
        if($id_adversario == $this->usuario_logado['id']){
            $msg = "Hey, $apelido, por que seu apelido está aqui? Deixa pra lá. Informe corretamente o adversário ou nada será feito!";
            $this->control_msg($msg);
        }
        
        $verifica_existencia = $this->Desafios_model->verifica_existencia_desafio($this->rodada_atual['rodada'], $id_adversario, $this->usuario_logado['id']);
        
        if ($verifica_existencia && $decisao == "novo") {
            $msg = "Já existe um desafio aceito ou pendente entre você e $apelido ;). É PRA GANHAR HEIN?! x1 É SAGRADO!!";
            $this->control_msg($msg);
        } else if(!$verifica_existencia && $decisao != "novo"){
            $msg = "Não existe desafio pendente entre você e o $apelido. Por que não desafia ele agora mesmo?";
            $this->control_msg($msg);
        }
        
        if($verifica_existencia['desafiador'] == $this->usuario_logado['id'] && $decisao == 'aceito'){
            $msg = "Seu mimado, você desafia e aceita? Espere o $apelido aceitar ou cancele se tiver com pressa :@";
            $this->control_msg($msg);
        }
        
        if($decisao == "novo"){
            $this->Desafios_model->registra_novo_desafio($this->rodada_atual['rodada'], $this->usuario_logado['id'], $id_adversario);
            $msg= "Você desafiou o $apelido! Representa nesse x1 hein?! Outra coisa, foi descontado 1 mango de você. Vença esse desafio e receba 2 de volta. Se ele não aceitar, no final da rodada devolveremos 1 mango";
        } else{
            $msg= $this->Desafios_model->decisao_desafio($this->rodada_atual['rodada'], $id_adversario, $this->usuario_logado['id'], $decisao);
        }
        $this->control_msg($msg);
    }
}
