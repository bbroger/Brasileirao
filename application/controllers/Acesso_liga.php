<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Acesso_liga extends CI_Controller {

    public function index() {
        $this->load->view('head');
        $this->load->view('acesso_liga');
    }

}
