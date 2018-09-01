<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Carrega a class Gerencia
 * 
 * Toda configuraçao de inserir/atualizar rodada. Irá também salvar os gols da partida e calcular os palpites.
 */
class Gerencia extends CI_Controller {

    /**
     * Carregará todos os times da serie A do ano atual
     * 
     * @var array
     */
    public $times;

    /**
     * Possuirá todas as rodadas cadastradas do bolao no ano atual contendo Data inicio e Data fim de cada rodada
     * 
     * @var array
     */
    public $rodadas_cadastradas;

    /**
     * Carrega o Gerencia_model e salva nas variaveis todos os times e as rodadas cadastradas.
     * 
     * @uses Gerencia_model::todos_times()         Tras a lista de todos os times da serie A e salva no Gerencia::times
     * @uses Gerencia_model::rodadas_cadastradas() Tras todas as rodadas cadastradas e salva no Gerencia::rodadas_cadastradas
     * @return void
     */
    public function __construct() {
        parent::__construct();

        $this->load->model('Gerencia_model');
        $times = $this->Gerencia_model->todos_times();
        foreach ($times as $key => $value) {
            $novo[$key]= $value['nome'];
        }
        $this->times= $novo;
        $this->rodadas_cadastradas = $this->Gerencia_model->rodadas_cadastradas();
    }

    /**
     * Confere se a rodada é um numero e está entre 1 e 38. Também confere se está no array
     * 
     * @uses array $rodadas_cadastradas                 Para consultar se a rodada existe nas rodadas cadastradas.
     * @used-by Gerencia::rodada()                      Confere a rodada para receber um numero
     * @used-by Gerencia::manipular_detalhes_rodada()   Confere a rodada para ver se é um numero válido e verifica se ja existe a rodada cadastrado.
     * @used-by Gerencia::verifica_adiou_rodada()       Confere a rodada para ver se ja existe a rodada cadastrado.
     * @used-by Gerencia::consultar_rodada()            Confere a rodada para receber um numero
     * @used-by Gerencia::enviar_resultado()            Confere a rodada para ver se é um numero válido e verifica se ja existe a rodada cadastrado.
     * @param int $recebe_rodada                        Recebe um numero para verificar se é rodada do bolao                 
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

    /**
     * Inicializa a pagina sem parametros.
     * 
     * @uses Gerencia::rodada();    Para carregar a view
     * @return void
     */
    public function index() {
        $this->rodada();
    }

    /**
     * Esse metodo irá montar a view passando parametros.
     * 
     * @uses Gerencia::confere_rodada()         Para montar a view, confere a rodada para retornar no formato válido.
     * @used-by Gerencia::valida_detalhes_rodada()       Se apresentar erros no form da rodada, é usado para montar a view e mostrar a msg de erro.
     * @param int       $recebe_rodada          Recebe uma rodada para consultar os dados e montar a view
     * @param string    $msg                    Envia uma mensagem a ser mostrada como parametro
     * @param string    $form                   Vai ser enviado como parametro da view para informar o Jquery que existiu uma requisição
     * @return void
     */
    public function rodada($recebe_rodada = null, $msg = null, $form = 0) {
        $confere_rodada = $this->confere_rodada($recebe_rodada);
        
        switch ($msg) {
            case 'new':
                $msg = "Rodada " . $confere_rodada['rodada'] . " cadastrado com sucesso";
                break;
            case 'update':
                $msg = "Rodada " . $confere_rodada['rodada'] . " atualizado com sucesso";
            default:
                break;
        }

        $dados = array(
            "rodada" => $confere_rodada['rodada'],
            "rodadas_cadastradas" => $this->rodadas_cadastradas,
            "times" => $this->times,
            "msg" => $msg,
            "form" => $form
        );

        $this->load->view('head', $dados);
        $this->load->view('gerencia');
    }

    /**
     * Vai verificar o tipo de açao para inserir/atualizar rodada.
     * 
     * @uses Gerencia::confere_rodada()        Confere a rodada para ver se é um numero válido e verifica se ja existe a rodada cadastrado.
     * @uses Gerencia::rodada()                Se apresentar erros no form da rodada, é usado para montar a view e mostrar a msg de erro.
     * @param int $recebe_rodada               Recebe a rodada para ser cadastrado/atualizado         
     * @return void
     */
    public function manipular_detalhes_rodada($recebe_rodada = null) {
        $confere_rodada = $this->confere_rodada($recebe_rodada);
        if (!$confere_rodada['status']) {
            $msg = "Rodada " . $confere_rodada['rodada'] . " inválido";
            $this->rodada($confere_rodada['rodada'], $msg);
        } else if ($this->input->post("manipular") == 'cadastrar' && $confere_rodada['existe']) {
            $msg = "Rodada " . $confere_rodada['rodada'] . " já está cadastrada";
            $this->rodada($confere_rodada['rodada'], $msg);
        } else if ($this->input->post("manipular") == 'atualizar' && !$confere_rodada['existe']) {
            $msg = "Rodada " . $confere_rodada['rodada'] . " não existe para ser atualizada";
            $this->rodada($confere_rodada['rodada'], $msg);
        } else {
            $this->valida_detalhes_rodada($confere_rodada['rodada']);
        }
    }

    /**
     * Depois que verificou qual será a ação da rodada, valida os dados.
     * 
     * @uses Gerencia::time_check()             Vai verificar se o time que foi submetido existe na lista dos times da serie A.
     * @uses Gerencia::acao_rodada()            Depois que validou o FORM salva no banco
     * @param int $rodada                       Recebe a rodada para salvar no banco
     * @return void
     */
    private function valida_detalhes_rodada($rodada) {
        $this->form_validation->set_rules('manipular', 'Manipular', 'trim|required|in_list[cadastrar,atualizar]');
        for ($i = 1; $i <= 10; $i++) {
            $this->form_validation->set_rules('time_mandante_' . $i, 'Mandante da partida ' . $i, 'callback_time_check', array('time_check' => 'Erro mandante da partida ' . $i));
            $this->form_validation->set_rules('time_visitante_' . $i, 'Visitante da partida ' . $i, 'callback_time_check', array('time_check' => 'Erro visitante da partida ' . $i));
            $this->form_validation->set_rules('local_partida_' . $i, 'Local da partida ' . $i, 'trim|max_length[20]');
            $this->form_validation->set_rules('data_partida_' . $i, 'Horario da partida ' . $i, 'callback_data_check', array('data_check' => 'Erro na data da partida ' . $i));
        }

        if ($this->form_validation->run()) {
            $this->acao_rodada($rodada, $this->input->post("manipular"));
        } else {
            $form = (validation_errors()) ? 1 : 0;
            $this->rodada($rodada, validation_errors(), $form);
        }
    }

    /**
     * Verifica se o time que foi enviado, está entre os 20 times da serie A.
     * 
     * @uses array $times                                    Lista dos 20 times
     * @used-by Gerencia::valida_detalhes_rodada()           Usado para validar os times.
     * @paren string $time                                   Recebe o time para validar
     * @return bool                                          Se o time existir na lista retorna true
     */
    public function time_check($time) {
        if (array_key_exists($time, $this->times)) {
            return true;
        }

        return false;
    }

    /**
     * Verifica se o horário da partida está correto.
     * 
     * @used-by Gerencia::valida_detalhes_rodada()           Vai verificar a data da partida
     * @paren string $horario                               Pode receber formato BR ou do banco.
     * @return bool                                         Se conseguiu converter a data do banco, retorna true
     */
    public function data_check($horario) {
        if ($horario == null) {
            return false;
        }

        //Se converter, a data veio no formato BR e é transformado na data do banco.
        $valida_horario_br = DateTime::createFromFormat("d/m/Y H:i", $horario);
        if ($valida_horario_br) {
            $horario = $valida_horario_br->format("Y-m-d H:i");
        }

        try {
            $hoje = new DateTime();
            //$hoje->add(new DateInterval("P2D"));
            $nova_data = new DateTime($horario);

            if ($nova_data < $hoje || $nova_data->format("Y") != $hoje->format("Y")) {
                return false;
            } else {
                return true;
            }
        } catch (Exception $ex) {
            return false;
        }
    }

    /**
     * Depois que validou os dados da rodada, aqui salvará no banco
     * 
     * @uses Gerencia::verifica_adiou_rodada()      Verifica se a data atual nao é maior que a data inicio da proxima rodada.
     * @uses Gerencia_model::salvar_nova_rodada()   Conecta e salva nova rodada no banco
     * @uses Gerencia_model::atualizar_rodada()   Conecta e atualiza rodada no banco
     * @used-by Gerencia::valida_detalhes_rodada()           Depois que validar, usa esse metodo para salvar
     * @param int $rodada                           Recebe uma rodada
     * @param string $acao                          Uma string para ver se é uma nova rodada ou é para atualizar
     * @return void
     */
    private function acao_rodada($rodada, $acao) {
        for ($i = 1; $i <= 10; $i++) {
            $dados_rodada[$i]["time_mandante_var"] = $this->input->post("time_mandante_" . $i);
            $dados_rodada[$i]["time_visitante_var"] = $this->input->post("time_visitante_" . $i);
            $dados_rodada[$i]["time_mandante"] = $this->times[$this->input->post("time_mandante_" . $i)];
            $dados_rodada[$i]["time_visitante"] = $this->times[$this->input->post("time_visitante_" . $i)];
            $dados_rodada[$i]["local_partida"] = ($this->input->post("local_partida_" . $i)) ? strtoupper($this->input->post("local_partida_" . $i)) : "SEM INFORMAÇÃO";
            $dados_rodada[$i]["data_partida"] = $this->input->post("data_partida_" . $i);
            $dados_rodada[$i]["adiou_partida"] = $this->verifica_adiou_rodada($rodada, $this->input->post("data_partida_" . $i));
        }

        if ($acao == "cadastrar") {
            $this->Gerencia_model->salvar_nova_rodada($rodada, $dados_rodada);
            redirect(base_url("Gerencia/rodada/$rodada/new"));
        } else {
            $this->Gerencia_model->atualizar_rodada($rodada, $dados_rodada);
            redirect(base_url("Gerencia/rodada/$rodada/update"));
        }
    }

    /**
     * Verifica se a data atual da partida é maior que a data inicio da proxima rodada. 
     * Se for, essa rodada será inserido como adiada e nao poderá usar essa data como fim dessa atual rodada.
     * 
     * @uses Gerencia::confere_rodada()             Verifica se existes rodadas cadastradas para ver se a proxima rodada existe
     * @used-by Gerencia::acao_valida()             Se a data da rodada for maior, retorna que adiou.
     * @return string                               Retorna sim ou nao para salvar no banco
     */
    private function verifica_adiou_rodada($rodada, $data) {
        $confere_rodada = $this->confere_rodada($rodada);
        if ($confere_rodada['existe_rodadas_cadastradas']) {
            $data_partida = new DateTime($data);
            if (array_key_exists($rodada - 1, $this->rodadas_cadastradas)) {
                $data_inicio_rodada_passada = new DateTime($this->rodadas_cadastradas[$rodada - 1]['inicio']);
            } else if(array_key_exists($rodada + 1, $this->rodadas_cadastradas)){
                $data_inicio_rodada_seguinte = new DateTime($this->rodadas_cadastradas[$rodada + 1]['inicio']);
            } else{
                return 'nao';
            }
        } else {
            return 'nao';
        }
        
        if (isset($data_inicio_rodada_passada) && $data_partida <= $data_inicio_rodada_passada) {
            return 'sim';
        }
        
        if (isset($data_inicio_rodada_seguinte) && $data_partida >= $data_inicio_rodada_seguinte) {
            return 'sim';
        }

        return 'nao';
    }

    //AJAX
    /**
     * Quando clicado no link próximo/anterior ou escolher uma rodada no SELECT,
     * O Ajax irá consultar se a rodada escolhida existe. Caso exista retornara detalhes da rodada
     * 
     * @uses Gerencia::confere_rodada()         Verifica a rodada para buscar os detalhes dela
     * @uses Gerencia_model::consultar_rodada() Para buscar os detalhes da rodada 
     * @param int $recebe_rodada                Recebe uma rodada
     * @return json                             Todos os detalhes da rodada.
     */
    public function consultar_rodada($recebe_rodada) {
        $confere_rodada = $this->confere_rodada($recebe_rodada);

        $tras_rodada = $this->Gerencia_model->consultar_rodada($confere_rodada['rodada']);
        $existe_rodada = ($tras_rodada) ? 1 : 0;

        $dados_rodada = array(
            "rodada" => $confere_rodada['rodada'],
            "existe_rodada" => $existe_rodada
        );

        if ($existe_rodada) {
            $dados_rodada["inicio"] = $this->rodadas_cadastradas[$confere_rodada['rodada']]["inicio"];
            $dados_rodada["fim"] = $this->rodadas_cadastradas[$confere_rodada['rodada']]["fim"];
            $dados_rodada["rodada_completa"] = $tras_rodada;
        }

        echo json_encode($dados_rodada);
    }

    /**
     * Ao enviar o resultado de uma partida, essa requisiçao vai receber via post partida, e os gols.
     * Se a partida náo começou, não envirá o resultado.
     * 
     * @uses Gerencia::confere_rodada()         Verifica a rodada para buscar os detalhes dela
     * @uses Gerencia_model::consultar_rodada() Para buscar os detalhes da rodada 
     * @uses Gerencia::calcula_palpites()       Depois que conferiu a rodada, irá calcular os pontos das palpites
     * @return json                             Retornara se foi inserido ou nao
     */
    public function enviar_resultado($recebe_rodada = null) {
        $confere_rodada = $this->confere_rodada($recebe_rodada);
        
        if (!$confere_rodada['status']) {
            echo json_encode(array("inseriu" => 0, "msg" => "Erro ao enviar o resultado. Rodada inválida!"));
        } else if (!is_numeric($this->input->post("gol_mandante")) || !is_numeric($this->input->post("gol_visitante"))) {
            echo json_encode(array("inseriu" => 0, "msg" => "Erro ao enviar o resultado. Insira apenas numeros nos gols mandante e visitante!"));
        } else if (!is_numeric($this->input->post("partida")) || $this->input->post("partida") < 1 || $this->input->post("partida") > 10) {
            echo json_encode(array("inseriu" => 0, "msg" => "Erro ao enviar o resultado. Partida inválido!"));
        } else {
            $rodada = $confere_rodada['rodada'];
            $partida = (int) $this->input->post("partida");
            $gol_mandante = (int) $this->input->post("gol_mandante");
            $gol_visitante = (int) $this->input->post("gol_visitante");
            $tras_rodada = $this->Gerencia_model->consultar_rodada($rodada);

            $data = new DateTime($tras_rodada[$partida - 1]["cad_data"]);
            $hoje = new DateTime();

            if ($hoje >= $data) {
                $this->calcula_palpites($rodada, $partida, $gol_mandante, $gol_visitante);
                echo json_encode(array("inseriu" => 1, "msg" => "Resultado inserido com sucesso!"));
            } else {
                echo json_encode(array("inseriu" => 0, "msg" => "Espere a partida iniciar para enviar o resultado."));
            }
        }
    }

    /**
     * Alem de salvar o resultado da partida, irá ver se existe palpites para fazer o calculo.
     * 
     * @uses Palpites_model::todos_palpites_partidas()      Irá pegar todos os palpites para ver se existe naquela partida
     * @uses Gerencia_model::salvar_gols_partida()          Salva os gols da partida
     * @param int $rodada                                   Rodada que está sendo enviado os gols
     * @param int $partida                                  Partida que está sendo enviado os gols
     * @param int $gol_mandante                             Gol mandante da partida enviada
     * @param int $gol_visitante                            Gol visitante da partida enviada
     * @return void
     */
    private function calcula_palpites($rodada, $partida, $gol_mandante, $gol_visitante) {
        $this->load->model('Palpites_model');
        $tras_palpites = $this->Palpites_model->todos_palpites_partidas($rodada, $partida);

        if ($tras_palpites) {
            $this->calcula_pontos_partida($rodada, $partida, $gol_mandante, $gol_visitante, $tras_palpites);
        }
        
        $this->Gerencia_model->salvar_gols_partida($rodada, $partida, $gol_mandante, $gol_visitante);
    }
    
    /**
     * Irá fazer o calculo de todos os palpites do bolao.
     * CC = Chute Certo. Acertou exatamente o resultado da partida exemplo: 1x1/1x1 4x0/4x0. Dá 100% (dobro) do valor apostado
     * CT = Chute na Trave. Acertou o vencedor da partida e a diferença de gol ou Acertou o empate e a diferença de gol por + ou - 1.
     *  Exemplo: 2x1/3x2 ou 1x0 = diferença por 1; 4x1/5x2 ou 3x0 = diferença por 3; 2x2/3x3 ou 1x1 = diferença por +- 1; 0x0/1x1 = diferença por 1. Dá 50% do valor apostado
     * CF = Chute Fora. Acertou o resultado mas nao o vencedor exemplo: 2x0/4x1 ou 3x1; 0x0/2x2 ou 4x4. Nào ganha nem perde
     * 
     * @uses Gerencia_model::salvar_resul_palpites      Depois que fez o calculo dos pontos, irá salvar nos palpites de cada usuario.
     * @param int $rodada                               Rodada que salvará os pontos
     * @param int $partida                              Partida que está sendo enviado os gols
     * @param int $gol_mandante                         Gol mandante da partida enviada
     * @param int $gol_visitante                        Gol visitante da partida enviada
     * @param array $tras_palpites                      Terá todos os palpites do bolao para aquela rodada.
     * @return void
     */
    private function calcula_pontos_partida($rodada, $partida, $gol_mandante, $gol_visitante, $tras_palpites) {
        foreach ($tras_palpites as $key => $value) {
            $cc = 0;
            $ct = 0;
            $cf = 0;
            $pontos = 0;
            $lucro = 0;
            $saldo = 0;

            if ($gol_mandante == $value["pap_gol_mandante"] && $gol_visitante == $value["pap_gol_visitante"]) {
                $cc = 1;
                $pontos = 5;
                $lucro = ($value["pap_aposta"]) ? $value["pap_aposta"] : 0;
                $saldo = ($value["pap_aposta"]) ? $value["pap_aposta"] + $lucro : 0;
            } else if ($gol_mandante > $gol_visitante && $value["pap_gol_mandante"] > $value["pap_gol_visitante"] && 
                    abs($gol_mandante - $gol_visitante) == abs($value["pap_gol_mandante"] - $value["pap_gol_visitante"])) {
                $ct = 1;
                $pontos = 3;
                $lucro = ($value["pap_aposta"]) ? $value["pap_aposta"] * 50 / 100 : 0;
                $saldo = ($value["pap_aposta"]) ? $value["pap_aposta"] + $lucro : 0;
            } 
            else if ($gol_mandante < $gol_visitante && $value["pap_gol_mandante"] < $value["pap_gol_visitante"] && 
                    abs($gol_mandante - $gol_visitante) == abs($value["pap_gol_mandante"] - $value["pap_gol_visitante"])) {
                $ct = 1;
                $pontos = 3;
                $lucro = ($value["pap_aposta"]) ? $value["pap_aposta"] * 50 / 100 : 0;
                $saldo = ($value["pap_aposta"]) ? $value["pap_aposta"] + $lucro : 0;
            } else if ($gol_mandante == $gol_visitante && abs($gol_mandante - $value["pap_gol_mandante"]) == 1 && abs($gol_visitante - $value["pap_gol_visitante"]) == 1) {
                $ct = 1;
                $pontos = 3;
                $lucro = ($value["pap_aposta"]) ? $value["pap_aposta"] * 50 / 100 : 0;
                $saldo = ($value["pap_aposta"]) ? $value["pap_aposta"] + $lucro : 0;
            } else if (($gol_mandante > $gol_visitante && $value["pap_gol_mandante"] > $value["pap_gol_visitante"])) {
                $cf = 1;
                $pontos = 1;
                $lucro = 0;
            } else if (($gol_mandante < $gol_visitante && $value["pap_gol_mandante"] < $value["pap_gol_visitante"])) {
                $cf = 1;
                $pontos = 1;
                $lucro = 0;
            } else if (($gol_mandante == $gol_visitante && $value["pap_gol_mandante"] == $value["pap_gol_visitante"])) {
                $cf = 1;
                $pontos = 1;
                $lucro = 0;
            } else {
                $lucro = ($value["pap_aposta"]) ? -($value["pap_aposta"] * 25 / 100) : 0;
                $saldo = ($value["pap_aposta"]) ? $value["pap_aposta"] + $lucro : 0;
            }

            $palpites[$value["pap_id_palpite"]]["cc"] = $cc;
            $palpites[$value["pap_id_palpite"]]["ct"] = $ct;
            $palpites[$value["pap_id_palpite"]]["cf"] = $cf;
            $palpites[$value["pap_id_palpite"]]["pontos"] = $pontos;
            $palpites[$value["pap_id_palpite"]]["lucro"] = $lucro;
            $palpites[$value["pap_id_palpite"]]["saldo"] = $saldo;
        }

        $this->Gerencia_model->salvar_resul_palpites($rodada, $partida, $palpites);
    }

}
