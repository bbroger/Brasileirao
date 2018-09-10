<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Carrega a Class Palpites
 * 
 * Toda configuraçao de inserir/atualizar palpites.
 */
class Palpites extends CI_Controller {

    /**
     * Possuirá todas as rodadas cadastradas do bolao no ano atual contendo Data inicio e Data fim de cada rodada
     * 
     * @var array
     */
    public $rodadas_cadastradas;
    
    /**
     * Carrega o Palpites_model e Gerencia_model. Também salva na variavel as rodadas cadastradas.
     * 
     * @uses Palpites_model                         Carrega o Palpites_model para utilizar na aplicação
     * @uses Gerencia_model::rodadas_cadastradas()  Tras todas as rodadas cadastradas e salva no Palpites::rodadas_cadastradas
     * @return void
     */
    public function __construct() {
        parent::__construct();
        $this->load->model("Palpites_model");
        $this->load->model("Gerencia_model");
        $this->load->library('Adm_lib');
        
        $this->rodadas_cadastradas = $this->Gerencia_model->rodadas_cadastradas();
    }
    
    /**
     * Inicializa a pagina sem parametros.
     * 
     * @uses Palpites::rodada()    Para carregar a view
     * @return void
     */
    public function index() {
        $this->rodada();
    }
    
    /**
     * Esse metodo irá montar a view passando parametros.
     * 
     * @uses Adm_lib::confere_rodada()          Para montar a view, confere a rodada para retornar no formato válido.
     * @param int       $recebe_rodada          Recebe uma rodada para consultar os dados e montar a view
     * @param string    $msg                    Envia uma mensagem a ser mostrada como parametro
     * @param string    $form                   Vai ser enviado como parametro da view para informar o Jquery que existiu uma requisição
     * @return void
     */
    public function rodada($recebe_rodada = null, $msg = null, $form = 0) {
        $confere_rodada = $this->adm_lib->confere_rodada($recebe_rodada);
        
        switch ($msg) {
            case 'completo':
                $msg = "Todas as partidas da rodada " . $confere_rodada['rodada'] . " foram palpitadas com sucesso!";
                break;
            case 'incompleto':
                $msg = "As partidas da rodada " . $confere_rodada['rodada'] . " que ainda não começaram, foram palpitadas com sucesso!";
            default:
                break;
        }

        $dados = array(
            "rodada" => $confere_rodada['rodada'],
            "rodadas_cadastradas" => $this->rodadas_cadastradas,
            "msg" => $msg,
            "form"=> $form
        );

        $this->load->view('head', $dados);
        $this->load->view('palpites');
    }
    
    //AJAX
    
    /**
     * Aqui irá trazer os palpites do usuario e os detalhes da rodada.
     * 
     * @uses Adm_lib::confere_rodada()              Confere a rodada para poder trazer os palpites corretamente.
     * @uses Palpites_model::palpites_usuario()     Irá trazer os palpites do usuario da rodada solicitado
     * @uses Gerencia_model::consultar_rodada()     Irá trazer os dados da rodada solicitada
     * @uses Palpites::rodadas_cadastradas          Se a rodada existe, irá consultar esse array para pegar inicio e fim
     * @param int $recebe_rodada                Rodada solicitada para trazer os palpites
     * @return json
     */
    public function palpites_usuario($recebe_rodada = null) {
        $confere_rodada = $this->adm_lib->confere_rodada($recebe_rodada);
        
        if ($confere_rodada['status']) {
            if($confere_rodada['existe']){
                $tras_palpites = $this->Palpites_model->palpites_usuario(1, $confere_rodada['rodada']);
                $usuario_palpitou= ($tras_palpites) ? 1 : 0;
                $tras_detalhes_rodada= $this->Gerencia_model->consultar_rodada($confere_rodada['rodada']);
                $tras_detalhes_times= $this->Gerencia_model->todos_times();
                
                $dados_palpites= array(
                    'existe_rodada'=> 1,
                    'rodada'=> $confere_rodada['rodada'],
                    'usuario_palpitou'=> $usuario_palpitou,
                    'palpites'=> $tras_palpites,
                    'detalhes_rodada'=> $tras_detalhes_rodada,
                    'detalhes_times'=> $tras_detalhes_times,
                    'inicio'=> $this->rodadas_cadastradas[$confere_rodada['rodada']]['inicio'],
                    'fim'=> $this->rodadas_cadastradas[$confere_rodada['rodada']]['fim'],
                    'msg'=> null
                );
            } else{
                $dados_palpites= array(
                    'existe_rodada'=> 0,
                    'rodada'=> $confere_rodada['rodada'],
                    'msg'=> 'Rodada '.$confere_rodada['rodada'].' não existe ainda para ser palpitado. Aguarde o bolão adiciona-lá'
                );
            }
        } else{
            $dados_palpites= array(
                'existe_rodada'=> 0,
                'rodada'=> $confere_rodada['rodada'],
                'msg'=> 'Rodada inválida.'
            );
        }

        echo json_encode($dados_palpites);
    }
    
    /**
     * Esse método irá receber a rodada e verificará se exite pelo menos uma partida que não começou para palpitar.
     * 
     * @uses Adm_lib::confere_rodada()              Confere a rodada para poder trazer os palpites corretamente.
     * @uses Palpites_model::palpites_usuarios()    Tras os palpites existentes por que se uma partida nao tiver autorizado, vai salvar null mesmo o usuario palpitar anteriormente
     * @uses Palpites::autoriza_palpites()          Antes de salvar, esse método irá verificar se existe pelo menos uma partida que não tenha começado
     * @uses Palpites::rodada()                     Se não tem nenhuma partida que nao tenha começado, avisa o usuario que nao foi palpitado pois ja começaram todas as partidas
     * @param type $recebe_rodada
     */
    public function enviar_palpites($recebe_rodada = null) {
        $confere_rodada = $this->adm_lib->confere_rodada($recebe_rodada);
        
        if (!$confere_rodada['status'] || !$confere_rodada['existe']) {
            $msg = "Palpite não enviado. Rodada invalida ou não cadastrada";
            $this->rodada($confere_rodada['rodada'], $msg);
        } else {
            $palpites_autorizados = $this->autoriza_palpites($confere_rodada['rodada']);

            if (in_array('sim', $palpites_autorizados)) {
                $palpites_existentes = $this->Palpites_model->palpites_usuario(1, $confere_rodada['rodada']);
                $this->salvar_palpites($palpites_existentes, $palpites_autorizados, $confere_rodada['rodada']);
            } else {
                $msg = "Palpite não enviado. Todas as partidas da rodada ".$confere_rodada['rodada']." foram iniciadas :(";
                $this->rodada($confere_rodada['rodada'], $msg);
            }
        }
    }
    
    /**
     * Esse método verificará partida por partida para ver se alguma data é menor que hoje para poder palpitar.
     * 
     * @uses Gerencia_model::consultar_rodada()         Tras todas as partidas da rodada para verificar sua data.
     * @used-by Palpites::enviar_palpites()             Irá retornar as datas autorizadas para ver se pode ou nao palpitar.
     * @param int $rodada                               Irá trazer todas as partidas dessa rodada e do ano atual
     * @return array
     */
    private function autoriza_palpites($rodada) {
        $rodada_completa = $this->Gerencia_model->consultar_rodada($rodada);
        $autoriza = array();

        foreach ($rodada_completa as $key => $value) {
            $data = new DateTime($value["cad_data"]);
            $hoje = new DateTime();

            if ($data > $hoje) {
                $autoriza[$value['cad_partida']]= 'sim';
            }
        }

        return $autoriza;
    }
    
    /**
     * A função aqui é pegar as partidas que podem ser palpitadas e obrigar que o usuario palpite ela.
     * 
     * @uses Palpites_model::salvar_palpites()          Irá salvar no banco os palpites permitidos da rodada.
     * @uses Palpites::rodada()                         Se tiver algum erro nos palpites, irá retornar com a mensagem.
     * @used-by Palpites::autoriza_palpites()           Depois que validou as datas que podem ser palpitadas, agora usará para pegar os palpites
     * @param array $palpites_existentes                Se existir, vai pegar os palpites anteriormente para colocar nas partidas nao autorizadas.
     * @param array $autoriza                           Está com todas as partidas que poderão ser palpitadas
     * @param int   $rodada                             Rodada que salvará os palpites
     * @return void
     */
    private function salvar_palpites($palpites_existentes, $autoriza, $rodada) {
        foreach ($autoriza as $key => $value) {
            $this->form_validation->set_rules("palpite_mandante_$key", "Mandante partida $key", "trim|integer|max_length[2]|required");
            $this->form_validation->set_rules("palpite_visitante_$key", "Visitante partida $key", "trim|required|integer|max_length[2]|required");
            $this->form_validation->set_rules("aposta_partida_$key", "Aposta partida $key", "trim|integer|max_length[4]");
        }
        
        if($this->form_validation->run()){
            $completo= 'completo';
            
            for($i = 1; $i <= 10; $i++){
                if(array_key_exists($i, $autoriza)){
                    $palpites[$i]["gol_mandante"]= (int) $this->input->post("palpite_mandante_".$i);
                    $palpites[$i]["gol_visitante"]= (int) $this->input->post("palpite_visitante_".$i);
                    $palpites[$i]["aposta"]= ($this->input->post("aposta_partida_".$i)) ? (int) $this->input->post("aposta_partida_".$i) : null;
                    $palpites[$i]["saldo"]= ($this->input->post("aposta_partida_".$i)) ? (int) -$this->input->post("aposta_partida_".$i) : 0;
                    $palpites[$i]["palpitou"]= 'sim';
                } else{
                    $palpites[$i]["gol_mandante"]= (array_key_exists($i-1, $palpites_existentes) ? $palpites_existentes[$i-1]['pap_gol_mandante'] : null);
                    $palpites[$i]["gol_visitante"]= (array_key_exists($i-1, $palpites_existentes) ? $palpites_existentes[$i-1]['pap_gol_visitante'] : null);
                    $palpites[$i]["aposta"]= (array_key_exists($i-1, $palpites_existentes) ? $palpites_existentes[$i-1]['pap_aposta'] : null);
                    $palpites[$i]["saldo"]= (array_key_exists($i-1, $palpites_existentes) ? -$palpites_existentes[$i-1]['pap_aposta'] : 0);
                    $palpites[$i]["palpitou"]= (array_key_exists($i-1, $palpites_existentes) ? $palpites_existentes[$i-1]['pap_palpitou'] : 'nao');
                    if(array_key_exists($i-1, $palpites_existentes) && $palpites_existentes[$i-1]['pap_palpitou'] == 'nao'){
                        $completo= 'incompleto';
                    }
                }
            }
            
            $this->Palpites_model->salvar_palpites(1, $rodada, $palpites);
            redirect(base_url("Palpites/rodada/$rodada/$completo"));
        } else{
            $form= (validation_errors()) ? 1: 0;
            $this->rodada($rodada, validation_errors(), $form);
        }
    }

}
