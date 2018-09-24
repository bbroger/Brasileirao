<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Adm_lib {

    /**
     * Carrega as funçoes do Codeigniter. Para usar faça $this->CI->...
     * 
     * @var object
     */
    private $CI;

    /**
     * ID do visitante será um ou seja, nao terá ninguém logado
     * 
     * @var int
     */
    public $usuario_logado;

    /**
     * Todas as rodadas cadastradas
     * 
     * @var array
     */
    private $rodadas_cadastradas;

    /**
     * Carrega as funçoes do Codeigniter e o Gerencia_model para pegar rodadas cadastradas.
     * Também verifica se existe usuario logado ou nao. Caso for visitante ou seja, nao exista logado, o ID será um e nao poderá fazer nada no bolao, apenas visualizar.
     * 
     * @return void
     */
    public function __construct() {
        $this->CI = & get_instance();
        if ($this->CI->session->has_userdata('id_usuario')) {
            $user['id'] = $this->CI->session->id_usuario;
            $user['logado'] = true;
            $this->usuario_logado = $user;
        } else {
            $user['id'] = 1;
            $user['logado'] = false;
            $this->usuario_logado = $user;
        }
        $this->CI->load->model('Gerencia_model');
        $this->rodadas_cadastradas = $this->CI->Gerencia_model->rodadas_cadastradas();
    }

    /**
     * Tras a rodada atual. Se hoje é menor que a data fim significa que a rodada atual ainda nao terminou
     * 
     * @used-by Portal                       Atraves da rodada atual, tras os dados do palpite
     * @used-by Desafios                     Precisa para manipular os desafios da rodada atual
     * @used-by Palpites                     A rodada atual faz com que toda vez que usuario entrar na tela palpites, ja cai na rodada atual os palpites.
     * @uses array $rodadas_cadastradas      Para consultar se a rodada existe nas rodadas cadastradas.
     * @return bool|array
     */
    public function rodada_atual() {
        if (!$this->rodadas_cadastradas) {
            $rodada['rodada'] = 0;
            $rodada['existe'] = false;
            return $rodada;
        }

        $hoje = new DateTime();
        foreach ($this->rodadas_cadastradas AS $key => $value) {
            $data_inicio = new DateTime($value['inicio']);
            $data_fim = new DateTime($value['fim']);
            if ($hoje <= $data_fim) {
                $rodada['rodada'] = $key;
                $rodada['existe'] = true;
                $rodada['inicio'] = $data_inicio->format("Y-m-d H:i:s");
                $rodada['fim'] = $data_fim->format("Y-m-d H:i:s");
                return $rodada;
            } else if ($key == 38) {
                $rodada['rodada'] = 38;
                $rodada['existe'] = true;
                $rodada['inicio'] = $data_inicio->format("Y-m-d H:i:s");
                $rodada['fim'] = $data_fim->format("Y-m-d H:i:s");
                return $rodada;
            }
        }
        //Se nao existir mais rodada atual, o jeito é pegar a ultima rodada cadastrada.
        //Pega a ultima valor do array, ou seja, o valor da ultima rodada cadastrada.
        end($this->rodadas_cadastradas);
        //Pega a key do ultimo valor, ou seja, a ultima rodada cadastrada.
        $ultima_rodada_cadastrada = key($this->rodadas_cadastradas);

        $data_inicio = new DateTime($this->rodadas_cadastradas[$ultima_rodada_cadastrada]['inicio']);
        $data_fim = new DateTime($this->rodadas_cadastradas[$ultima_rodada_cadastrada]['fim']);
        $rodada['rodada'] = $ultima_rodada_cadastrada;
        $rodada['existe'] = false;
        $rodada['inicio'] = $data_inicio->format("Y-m-d H:i:s");
        $rodada['fim'] = $data_fim->format("Y-m-d H:i:s");
        return $rodada;
    }

    /**
     * Confere se a rodada é um numero e está entre 1 e 38. Também confere se está no array
     *
     * @used-by Gerencia
     * @used-by Palpites
     * @uses array $rodadas_cadastradas                 Para consultar se a rodada existe nas rodadas cadastradas.
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

    /**
     * Retorna em tempo real todos os dados do usuario. OS mangos são atualizados em tempo real, descontando até mesmo o que ele apostou na rodada atual
     * Inclusive os desafios e as inscriçoes das copas, tudo em tempo real.
     * 
     * @used-by Adm_lib::total_mangos_usuario()                         Irá pegar apenas os lucros e calcular o total de mangos que o usuario possui
     * @used-by Desafios_model::todos_adversarios()                     Consulta o ID de caad usuario para mostrar os dados no desafio.
     * @uses User_model::dados()                                        Tras os dados do usuario
     * @uses Classificacao_model::total_consulta_classif_user()         Tras em tempo real o saldo do usuario (-aposta + lucro)
     * @uses Desafios_model::total_dados_desafio_user()                 Tras o total de desafios aceitos/pendentes, desafiado/desafiador e quantos venceu.
     * @uses Copa_model::total_dados_copa_oficial_user()                Tras o total de partic de copas oficiais, se existir os titulos, a rodada que venceu, o id_copa e o numero de inscritos.          
     * @param int $id
     * @return array
     */
    public function todos_dados_usuarios($id = null) {
        $this->CI->load->model('User_model');
        $dados_user = $this->CI->User_model->dados($id);

        $this->CI->load->model('Classificacao_model');
        $dados_classif = $this->CI->Classificacao_model->total_consulta_classif_user($id);

        $this->CI->load->model('Desafios_model');
        $dados_desafio = $this->CI->Desafios_model->total_dados_desafio_user($id);

        $this->CI->load->model('Copa_model');
        $dados_copa = $this->CI->Copa_model->total_dados_copa_oficial_user($id);

        $arr = array(
            'usuario' => $dados_user,
            'classif' => $dados_classif,
            'desafios' => $dados_desafio,
            'copas' => $dados_copa
        );

        return $arr;
    }

    /**
     * Pega todos os lucros do usuario e soma para ter o total de mangos
     * 
     * @used-by Palpites                            No _construct, salva no atributo e será usado para validar novas apostas nos palpites
     * @used-by Desafios                            No _construct, pega o mango para caso ele desafiar ou aceitar, verificar se tem mangos suficiente
     * @uses Adm_lib::todos_dados_usuario()         Irá pegar o lucro para somar.
     * @param int $id
     * @return type
     */
    public function total_mangos_usuario($id) {
        $dados = $this->todos_dados_usuarios($id);

        $mangos_recebido = $dados['usuario']['use_mangos'];
        $saldo_classif = ($dados['classif']) ? $dados['classif']['total_saldo'] : 0;
        $saldo_desafios = $dados['desafios']['venceu'] * 2 - ($dados['desafios']['total_aceitos'] + $dados['desafios']['total_pendentes']);
        $total_partic_copa = $dados['copas']['total'];
        if ($dados['copas']['venceu']) {
            $total_lucro_copa = 0;
            foreach ($dados['copas']['venceu'] as $key => $value) {
                $total_lucro_copa += $value['inscritos'] * 3;
            }
        } else {
            $total_lucro_copa = 0;
        }
        $saldo_copa = $total_lucro_copa - $total_partic_copa * 3;
        $total_mangos = $mangos_recebido + $saldo_classif + $saldo_desafios + $saldo_copa;

        return $total_mangos;
    }

}
