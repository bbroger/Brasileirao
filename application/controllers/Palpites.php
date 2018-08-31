<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Palpites extends CI_Controller {

    public $rodadas_cadastradas;

    public function __construct() {
        parent::__construct();
        $this->load->model("Palpites_model");
        $this->load->model("Gerencia_model");
        $this->rodadas_cadastradas = $this->Gerencia_model->rodadas_cadastradas();
    }

    public function index() {
        $this->rodada();
    }

    public function rodada($rodada = null, $msg = null, $form= null) {
        if ($rodada == null || !is_numeric($rodada) || $rodada <= 0 || $rodada > 38) {
            $rodada = 1;
        }

        $dados = array(
            "rodada" => $rodada,
            "rodadas_cadastradas" => $this->rodadas_cadastradas,
            "msg" => $msg,
            "form"=> $form
        );

        $this->load->view('head', $dados);
        $this->load->view('palpites');
    }

    public function palpites_usuario($rodada = null, $msg = null) {
        if ($rodada == null || !is_numeric($rodada) || $rodada <= 0 || $rodada > 38) {
            $rodada = 1;
        }

        $tras_palpites = $this->Palpites_model->palpites_usuario(1, $rodada);
        $existe_rodada = ($tras_palpites) ? 1 : 0;

        $dados_palpites = array(
            "existe_rodada" => $existe_rodada,
            "rodada" => $rodada
        );

        if ($existe_rodada) {
            $dados_palpites["inicio"] = $this->rodadas_cadastradas[$rodada]["inicio"];
            $dados_palpites["fim"] = $this->rodadas_cadastradas[$rodada]["fim"];
            $dados_palpites["palpites_completo"] = $tras_palpites;
        }

        echo json_encode($dados_palpites);
    }

    public function enviar_palpites($rodada = null) {
        if ($rodada == null || !is_numeric($rodada) || $rodada <= 0 || $rodada > 38 || !array_key_exists($rodada, $this->rodadas_cadastradas) || count($this->input->post()) == 0) {
            $msg = "Palpite não enviado. Rodada invalida ou não cadastrada";
            $this->rodada(1, $msg);
        } else {
            $palpites_autorizados = $this->autoriza_palpites($rodada);

            if (count($palpites_autorizados) > 0) {
                $this->salvar_palpites($palpites_autorizados, $rodada);
            } else {
                $msg = "Palpite não enviado. Todas as partidas foram iniciadas.";
                $this->rodada($rodada, $msg);
            }
        }
    }

    private function autoriza_palpites($rodada) {
        $rodada_completa = $this->Gerencia_model->consultar_rodada($rodada);
        $autoriza = array();

        foreach ($rodada_completa as $key => $value) {
            $data = new DateTime($value["cad_data"]);
            $hoje = new DateTime();

            if ($hoje <= $data) {
                array_push($autoriza, $value["cad_partida"]);
            }
        }

        return $autoriza;
    }

    private function salvar_palpites($autoriza, $rodada) {
        foreach ($autoriza as $key => $value) {
            $this->form_validation->set_rules("palpite_mandante_$value", "Mandante partida $value", "trim|required|integer|max_length[2]");
            $this->form_validation->set_rules("palpite_visitante_$value", "Visitante partida $value", "trim|required|integer|max_length[2]");
            $this->form_validation->set_rules("aposta_partida_$value", "Aposta partida $value", "trim|integer");
        }
        
        if($this->form_validation->run()){
            foreach ($autoriza as $key => $value) {
                $palpites[$value]["mandante"]= $this->input->post("palpite_mandante_$value");
                $palpites[$value]["visitante"]= $this->input->post("palpite_visitante_$value");
                $palpites[$value]["aposta"]= $this->input->post("aposta_partida_$value");
            }
            
            $this->Palpites_model->salvar_palpites($rodada, $palpites);
            redirect(base_url("Palpites/rodada/$rodada"));
        } else{
            $form= (validation_errors()) ? 1: NULL;
            $this->rodada($rodada, validation_errors(), $form);
        }
    }

}
