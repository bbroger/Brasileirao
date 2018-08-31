<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Teste extends CI_Controller {

    public function index() {
        $ar= array();
        
        if((count($ar) == 0)){
            echo 1;
        } else{
            echo 2;
        }
    }

}
