<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Teste extends CI_Controller {

    public function index() {
        $arr['s']['teste']= 3;
        $arr['s']['palpitou']= 'sim';
        $arr['r']['teste']= 5;
        $arr['r']['palpitou'] = 'nao';
        
        var_dump(array_values($arr));
    }

}
