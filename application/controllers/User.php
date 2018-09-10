<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Carrega a class User
 * 
 * Todos os dados do usuario e criacao de um novo
 */
class User extends CI_Controller {

    /**
     * Possuirá todas as rodadas cadastradas do bolao no ano atual contendo Data inicio e Data fim de cada rodada
     * 
     * @var array
     */
    public $rodadas_cadastradas;
    
    /**
     * Mangos que cada usuario recebe.
     * 
     * @var float
     */
    private $mangos_padrao= 100.00;
    
    /**
     * Irá somar o saldo de todas as rodadas, desafios e torneios.
     * 
     * @var float
     */
    public $total_mangos;
    

    /**
     * Carrega o Gerencia_model e salva nas variaveis as rodadas cadastradas.
     * 
     * @uses User_model                             Carrega o User_model para utilizar na aplicação
     * @uses Gerencia_model::rodadas_cadastradas()  Tras todas as rodadas cadastradas e salva no Gerencia::rodadas_cadastradas
     * @return void
     */
    public function __construct() {
        parent::__construct();
        $this->load->model('User_model');
        $this->load->model('Gerencia_model');
        $this->rodadas_cadastradas = $this->Gerencia_model->rodadas_cadastradas();
    }

    /**
     * Confere se a rodada é um numero e está entre 1 e 38. Também confere se está no array
     * 
     * @uses array $rodadas_cadastradas                 Para consultar se a rodada existe nas rodadas cadastradas.              
     * @return array
     */
    private function confere_rodada($recebe_rodada) {
        $confere_rodada['existe_rodadas_cadastradas'] = true;
        if ($recebe_rodada == null || !is_numeric($recebe_rodada) || $recebe_rodada <= 0 || $recebe_rodada > 38) {
            $confere_rodada['status'] = false;
            $confere_rodada['rodada'] = 1;
        } else {
            $confere_rodada['status'] = true;
            $confere_rodada['rodada'] = (int) $recebe_rodada;
        }
        if (count($this->rodadas_cadastradas) == 0) {
            $confere_rodada['existe_rodadas_cadastradas'] = false;
            $confere_rodada['existe'] = false;
        } else {
            if (array_key_exists($recebe_rodada, $this->rodadas_cadastradas)) {
                $confere_rodada['existe'] = true;
            } else {
                $confere_rodada['existe'] = false;
            }
        }

        return $confere_rodada;
    }
}
