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
     * @used-by Copa                         Se nao existe rodada atual nao pode cadastrar em copas.
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
     * @used-by Copa                                    Pega a rodada para consultar os inscritos da copa.
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
     * @used-by Copa                                                    No __construct, pega as copas do usuario
     * @used-by Copa::monta_copa()                                      Irá pegar apelido, imagem do perfil e os titulos de cada participante.
     * @uses User_model::dados()                                        Tras os dados do usuario
     * @uses Classificacao_model::total_consulta_classif_user()         Tras em tempo real o saldo do usuario (-aposta + lucro)
     * @uses Desafios_model::total_desafios_por_id()                    Tras o total de desafios aceitos/pendentes, desafiado/desafiador e quantos venceu.
     * @uses Copa_model::total_copas_por_id()                           Tras o total de partic de copas, titulos, a rodada que venceu, o id_copa e o numero de inscritos.          
     * @param int   $id
     * @param bool  $filtro                                             Recebe um array com o filtro das informaçoes que deseja
     * @return array
     */
    public function todos_dados_usuarios($id, $filtro) {
        if (in_array('usuario', $filtro)) {
            $this->CI->load->model('User_model');
            $arr['usuario'] = $this->CI->User_model->dados($id);
        }

        if (in_array('classificacao', $filtro)) {
            $this->CI->load->model('Classificacao_model');
            $arr['classificacao'] = $this->CI->Classificacao_model->total_consulta_classif_user($id);
        }

        if (in_array('desafios', $filtro)) {
            $this->CI->load->model('Desafios_model');
            $arr['desafios'] = $this->CI->Desafios_model->total_desafios_por_id($id);
        }

        if (in_array('copas', $filtro)) {
            $this->CI->load->model('Copa_model');
            $arr['copas'] = $this->CI->Copa_model->total_copas_por_id($id);
        }

        return $arr;
    }

    /**
     * Pega todos os lucros do usuario e soma para ter o total de mangos
     * 
     * @used-by Palpites                            No _construct, salva no atributo e será usado para validar novas apostas nos palpites
     * @used-by Desafios                            No _construct, pega o mango para caso ele desafiar ou aceitar, verificar se tem mangos suficiente
     * @used-by Copa                                No _construct, pega o mango para se cadastrar na copa
     * @uses Adm_lib::todos_dados_usuario()         Irá pegar o lucro para somar.
     * @param int $id
     * @return type
     */
    public function total_mangos_usuario($id) {
        $dados = $this->todos_dados_usuarios($id, array('usuario', 'classificacao', 'desafios', 'copas'));

        $mangos_recebido = $dados['usuario']['use_mangos'];
        $saldo_classif = ($dados['classificacao']) ? $dados['classificacao']['total_saldo'] : 0;
        $saldo_desafios = ($dados['desafios']) ? $dados['desafios']['saldo'] : 0;
        $saldo_copa = 0;
        if ($dados['copas']) {
            foreach ($dados['copas'] AS $key => $value) {
                if (is_numeric($key)) {
                    $saldo_copa += $value['saldo'];
                }
            }
        }
        $total_mangos = $mangos_recebido + $saldo_classif + $saldo_desafios + $saldo_copa;

        return $total_mangos;
    }

    /**
     * Aqui pega a classificação do bolao inteiro, soma tudo e ordena conforme solicitado no parametro
     * 
     * @used-by Classificacao::classificacao()          Pega a classif geral, mangos e desafios para apresentar na view
     * @used-by Copa::inscricao_copa()                  Vai pegar as classif para verificar se pode ou nao se cadastrar nas copas
     * @uses User_model::dados()                        Trás todos os usuários do bolao. É OBRIGATORIO TER PELO MENOS UM USUARIO.
     * @uses Classificacao_model::classif_geral()       Trás todos os dados do palpites do usuario, pontos, mangos, cc, ct, cf...
     * @uses Desafios_model::total_desafios()           Trás todos os desafios do bolao bem como total, vencidos e saldos.
     * @uses Copa_model::total_copas()                  Trás todas as copas dos usuarios bem como, total de inscritos, chave e o campeao
     * @uses Adm_lib::ordena_classif()                  Ordena por pontos
     * @uses Adm_lib::ordena_mangos()                   Ordena por mangos
     * @uses Adm_lib::ordena_deesafios()                Ordena por desafios
     * @param String $ordena                            Informa qual tipo de ordenaçao deseja, por pontos, mangos etc...
     * @return array
     */
    public function classif_geral($ordena) {
        $rodada_atual = $this->rodada_atual();
        $rodada = $rodada_atual['rodada'];

        $this->CI->load->model('User_model');
        $usuarios = $this->CI->User_model->dados();

        $classif = $this->CI->Classificacao_model->classif_geral($rodada);

        $this->CI->load->model('Desafios_model');
        $desafios = $this->CI->Desafios_model->total_desafios($rodada);

        $this->CI->load->model('Copa_model');
        $copa = $this->CI->Copa_model->total_copas($rodada);

        foreach ($usuarios AS $key => $value) {
            $dados[$key + 1]['id'] = $value['use_id_user'];
            $dados[$key + 1]['apelido'] = $value['use_nickname'];
            $dados[$key + 1]['nome'] = $value['use_name'];
            $dados[$key + 1]['img_perfil'] = base_url("assets/images/perfil/" . $value['use_img_perfil']);
            $dados[$key + 1]['mangos'] = $value['use_mangos'];
            if (array_key_exists($key + 1, $classif)) {
                $dados[$key + 1]['cc'] = $classif[$key + 1]['cc'];
                $dados[$key + 1]['ct'] = $classif[$key + 1]['ct'];
                $dados[$key + 1]['cf'] = $classif[$key + 1]['cf'];
                $dados[$key + 1]['pontos'] = $classif[$key + 1]['pontos'];
                $dados[$key + 1]['mangos'] += $classif[$key + 1]['saldo'];
            } else {
                $dados[$key + 1]['cc'] = 0;
                $dados[$key + 1]['ct'] = 0;
                $dados[$key + 1]['cf'] = 0;
                $dados[$key + 1]['pontos'] = 0;
            }
            if (array_key_exists($key + 1, $desafios)) {
                $dados[$key + 1]['defr_aceito'] = $desafios[$key + 1]['defr_aceito'];
                $dados[$key + 1]['def_aceito'] = $desafios[$key + 1]['def_aceito'];
                $dados[$key + 1]['venceu'] = $desafios[$key + 1]['venceu'];
                $dados[$key + 1]['mangos'] += $desafios[$key + 1]['saldo'];
            } else {
                $dados[$key + 1]['defr_aceito'] = 0;
                $dados[$key + 1]['def_aceito'] = 0;
                $dados[$key + 1]['venceu'] = 0;
            }
            if (array_key_exists($key + 1, $copa)) {
                foreach ($copa[$key + 1] as $chave => $valor) {
                    $dados[$key + 1]['mangos'] += $valor['saldo'];
                }
            }
        }

        if ($ordena == 'geral') {
            return $this->ordernar_classif($dados);
        } else if ($ordena == 'mangos') {
            return $this->ordernar_mangos($dados);
        } else {
            return $this->ordernar_desafios($dados);
        }
    }

    /**
     * Ordena a classificacao por pontos
     * 
     * @used-by Adm_lib::classif_geral()            Recece como paramentro para ordenar por pontos
     * @uses Adm_lib::chave_array()                 Quando recebe o parametro para ordenar, esse metodo pega toda a coluna para ser ordenado
     * @param String $dados
     * @return array
     */
    private function ordernar_classif($dados) {
        $pontos = $this->chave_array($dados, 'pontos');
        $mangos = $this->chave_array($dados, 'mangos');
        $cc = $this->chave_array($dados, 'cc');
        $ct = $this->chave_array($dados, 'ct');
        $cf = $this->chave_array($dados, 'cf');
        $id = $this->chave_array($dados, 'id');
        array_multisort($pontos, SORT_DESC, $mangos, SORT_DESC, $cc, SORT_DESC, $ct, SORT_DESC, $cf, SORT_DESC, $id, SORT_ASC, $dados);

        return $dados;
    }

    /**
     * Ordena a classificacao por mangos
     * 
     * @used-by Adm_lib::classif_geral()            Recece como paramentro para ordenar por mangos
     * @uses Adm_lib::chave_array()                 Quando recebe o parametro para ordenar, esse metodo pega toda a coluna para ser ordenado
     * @param String $dados
     * @return array
     */
    private function ordernar_mangos($dados) {
        $pontos = $this->chave_array($dados, 'pontos');
        $mangos = $this->chave_array($dados, 'mangos');
        $cc = $this->chave_array($dados, 'cc');
        $ct = $this->chave_array($dados, 'ct');
        $cf = $this->chave_array($dados, 'cf');
        $id = $this->chave_array($dados, 'id');
        array_multisort($mangos, SORT_DESC, $pontos, SORT_DESC, $cc, SORT_DESC, $ct, SORT_DESC, $cf, SORT_DESC, $id, SORT_ASC, $dados);

        return $dados;
    }

    /**
     * Ordena a classificacao por desafios
     * 
     * @used-by Adm_lib::classif_geral()            Recece como paramentro para ordenar por desafios
     * @uses Adm_lib::chave_array()                 Quando recebe o parametro para ordenar, esse metodo pega toda a coluna para ser ordenado
     * @param String $dados
     * @return array
     */
    private function ordernar_desafios($dados) {
        $venceu = $this->chave_array($dados, 'venceu');
        $defr_aceito = $this->chave_array($dados, 'defr_aceito');
        $def_aceito = $this->chave_array($dados, 'def_aceito');
        $pontos = $this->chave_array($dados, 'pontos');
        $mangos = $this->chave_array($dados, 'mangos');
        $id = $this->chave_array($dados, 'id');
        array_multisort($venceu, SORT_DESC, $defr_aceito, SORT_DESC, $def_aceito, SORT_DESC, $pontos, SORT_DESC, $mangos, SORT_DESC, $id, SORT_ASC, $dados);

        return $dados;
    }

    private function chave_array($arr, $col) {
        foreach ($arr as $key => $value) {
            $sort_data[$key] = $value[$col];
        }

        return $sort_data;
    }

    /**
     * As copas do bolão. Pode cadastrar mais sem problemas.
     * 
     * @used-by Copa            No __construct, envia para variavel global $copas
     * @used-by Copa_model      No __construct, envia para variavel global $copas
     * @return array
     */
    public function copas() {
        $copas = array(
            1 => array('nome' => 'Copa da Liga', 'primeiro' => 1, 'ultimo' => 33, 'entrada' => 3),
            2 => array('nome' => 'Copa Capitalista', 'primeiro' => 4, 'ultimo' => 32, 'entrada' => 5),
            3 => array('nome' => 'Copa Desafiante', 'primeiro' => 4, 'ultimo' => 32, 'entrada' => 5),
            4 => array('nome' => 'Copa Lendários', 'primeiro' => 4, 'ultimo' => 32, 'entrada' => 5)
        );
        
        return $copas;
    }

}
