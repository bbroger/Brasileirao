<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Teste extends CI_Controller {

    public function index() {
        $this->session->set_userdata('id_usuario', 2);
        
        $dados[1]['inicio']= 2;
        $dados[1]['termino']= 3;
        
        var_dump(end($dados));
        var_dump(key($dados));
    }

}
