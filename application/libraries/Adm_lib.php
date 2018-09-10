<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Adm_lib {
    private $CI;
    private $rodadas_cadastradas;
    
    public function __construct() {
        $this->CI = & get_instance();
        $this->CI->load->model('Gerencia_model');
        $this->rodadas_cadastradas = $this->CI->Gerencia_model->rodadas_cadastradas();
    }
    
    /**
     * Tras a rodada atual. Se hoje é menor que a data fim significa que a rodada atual ainda nao terminou
     * 
     * @uses array $rodadas_cadastradas      Para consultar se a rodada existe nas rodadas cadastradas.
     * @return bool|array
     */
    public function rodada_atual(){
        if(!$this->rodadas_cadastradas){
            return false;
        }
        
        $hoje= new DateTime();
        foreach($this->rodadas_cadastradas AS $key=>$value){
            $data_fim= new DateTime($value['fim']);
            if($hoje <= $data_fim){
                return $key;
            }
        }
        
        return false;
    }
    
    

    /**
     * Confere se a rodada é um numero e está entre 1 e 38. Também confere se está no array
     * 
     * @uses array $rodadas_cadastradas                 Para consultar se a rodada existe nas rodadas cadastradas.
     * @used-by Gerencia
     * @used-by Palpites
     * @param int $recebe_rodada                        Recebe um numero para verificar se é rodada do bolao                 
     * @return array
     */
    public function confere_rodada($recebe_rodada) {
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
