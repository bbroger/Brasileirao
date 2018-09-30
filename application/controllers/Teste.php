<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Teste extends CI_Controller {

    public function index() {
        $this->session->set_userdata('id_usuario', 2);
        
        $dado= array();
        
        if($dado){
            echo 1;
        } else{
            echo 2;
        }
    }

}
