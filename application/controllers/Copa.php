<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Carrega a Class Copa
 * 
 * Aqui irá fazer novos cadastros, atualizaçoes e consultar copas
 */
class Copa extends CI_Controller {

    /**
     * O ID do usuario logado
     * 
     * @var int
     */
    private $usuario_logado;

    /**
     * Total de mangos em tempo real e da data atual.
     * 
     * @var float
     */
    private $mangos_total;

    /**
     * Pega a rodada atual
     * 
     * @var int
     */
    private $rodada_atual;

    /**
     * As 4 copas estao aqui.
     * @var array 
     */
    private $copas = array(
        1 => array('nome' => 'copa_liga', 'primeiro' => 1, 'ultimo' => 33, 'entrada' => 3),
        2 => array('nome' => 'copa_capitalista', 'primeiro' => 4, 'ultimo' => 32, 'entrada' => 5),
        3 => array('nome' => 'copa_desafiante', 'primeiro' => 4, 'ultimo' => 32, 'entrada' => 5),
        4 => array('nome' => 'copa_lendario', 'primeiro' => 4, 'ultimo' => 32, 'entrada' => 5)
    );

    /**
     * Todas as rodadas cadastradas
     * 
     * @var array
     */
    private $rodadas_cadastradas;

    /**
     * Carrega o Copa_model, Liga_model e Adm. Pega a rodada atual.
     * 
     * @uses Copa_model                           Carrega o Copa_model para utilizar na aplicação
     * @uses Adm_lib::total_mangos_usuarios()     Tras a rodada atual
     * @return void
     */
    public function __construct() {
        parent::__construct();

        $this->load->model('Copa_model');
        $this->load->model('Liga_model');
        $this->load->library('Adm_lib');
        $this->load->model('Gerencia_model');
        $this->rodadas_cadastradas = $this->Gerencia_model->rodadas_cadastradas();
        $this->rodada_atual = $this->adm_lib->rodada_atual();
        $this->usuario_logado = $this->adm_lib->usuario_logado;
        $this->mangos_total = $this->adm_lib->total_mangos_usuario($this->usuario_logado['id']);
    }

    /**
     * Irá receber uma mensagem e redirecionar para o portal
     * 
     * @param String $msg
     * @return void
     */
    private function control_msg($msg) {
        $this->session->set_userdata('control_msg', $msg);
        redirect(base_url("Portal/opcao/copa/"));
    }

    /**
     * Inicializa a pagina sem parametros.
     * 
     * @uses Copa::opcao()    Para carregar a view
     * @return void
     */
    public function index() {
        $this->opcao();
    }

    /**
     * Esse metodo irá montar a view passando parametros.
     * 
     * @uses Portal::rodada_atual               Rodada atual do bolao
     * @uses Portal::usuario_logado             Se existe um usuario logado
     * @param String $msg                       Uma palavra chave para mostrar a mensagem
     * @return void
     */
    public function opcao($msg = null) {
        $dados = array(
            "usuario_logado" => $this->usuario_logado,
            "msg" => $msg,
            "rodada_atual" => $this->rodada_atual['rodada']
        );

        $this->load->view('head', $dados);
        $this->load->view('copa');
    }

    /**
     * Quando o usuário for se inscrever na copa, esse metodo confere qual é a copa e qual a liga.
     * 
     * @uses Portal::rodada_atual               Rodada atual do bolao para se inscrever nas copas
     * @uses Portal::copas                      Tem as copas, ids, preços entre outros....
     * @uses Portal::mangos_total               Para entrar na copa tem preço, por isso confere os mangos para se inscrever.
     * @uses Liga_model::verifica_liga          Consulta o ID da liga para ver se existe ou se está ativo.
     * @uses Liga_model::verifica_user_liga     Verifica se o usuário realmente está na liga que foi submetido.
     * @uses Portal::rodadas_copas              Depois que verificou copas e ligas, aplicar as regras de negocio da copa
     * @uses Portal::usuario_logado             Pega o usuário logado para verificar se esse usuario participa da liga submetida
     * @param String $msg                       Uma palavra chave para mostrar a mensagem
     * @return void
     */
    public function verifica_copas() {
        if (!$this->rodada_atual['existe']) {

            $msg = "Não existe rodada cadastrada no momento. Por favor aguarde ser cadastrado para se inscrever na copa.";
            $this->control_msg($msg);
        }

        $this->form_validation->set_rules("copa", "<strong>Copa</strong>", "trim|required|integer|in_list[1,2,3,4]");

        if (!$this->form_validation->run()) {
            $msg = "Copa inválida. Não existe essa copa informado.";
            $this->control_msg($msg);
        }

        $id_copa = $this->input->post('copa');
        $id_liga = null;

        if ($this->mangos_total < $this->copas[$id_copa]['entrada']) {
            $msg = "Você não tem mangos suficientes para entrar na Copa. A entrada custa ".$this->copas[$id_copa]['entrada']." e você possiu $this->mangos_total";
            $this->control_msg($msg);
        }

        if ($id_copa == 1) {
            $this->form_validation->set_rules("liga", "<b>Liga</b>", "trim|required|integer");
            if (!$this->form_validation->run()) {
                $msg = validation_errors();
                $this->control_msg($msg);
            }
            
            $id_liga = $this->input->post('liga');
            $verifica_liga = $this->Liga_model->verifica_liga($id_liga);

            if (!$verifica_liga) {
                $msg = "Não foi possível entrar na copa, essa liga não existe.";
                $this->control_msg($msg);
            }
            
            $verifica_membro = $this->Liga_model->verifica_user_liga($id_liga, $this->usuario_logado['id']);
            
            if (!$verifica_membro) {
                $msg = "Não foi possível entrar na copa, você não é membro dessa liga.";
                $this->control_msg($msg);
            }
        }

        $this->rodada_copas($id_copa, $id_liga);
    }

    /**
     * Depois que validou copa e liga, pega a rodada e verifica se a data permite a inscriçao
     * 
     * @used Portal::verifica_copas             Recebe o ID copa e liga para validar.
     * @uses Portal::rodada_atual               Rodada atual para calcular e pegar a rodada das copas.
     * @uses Portal::copas                      Pega o inicio e termino da copa submetida
     * @uses Portal::rodadas_cadastradas        Pega as rodadas cadastradas para ver se a rodada da copa existe e pega a data inicio.
     * @uses Copa_model::verifica_inscrito      Verifica se o usuário já se inscreveu na copa.
     * @uses Portal::inscricao_copa             Depois que pegou a copa e a rodada, na proxima etapa pegará a vaga e se está apto para inscrever.
     * @param String $msg                       Uma palavra chave para mostrar a mensagem
     * @return void
     */
    private function rodada_copas($id_copa, $id_liga) {
        $rodada_atual = $this->rodada_atual['rodada'];
        $inicio_copa = $this->copas[$id_copa]['primeiro'];
        $termino_copa = $this->copas[$id_copa]['ultimo'];
        $rodada_copa = false;
        
        for ($i = $inicio_copa; $i <= $termino_copa; $i += 4) {
            if ($rodada_atual <= $i) {
                $rodada_copa = $i;
                break;
            }
        }

        if (!$rodada_copa) {
            $msg = "Não existe copa disponível no momento.";
            $this->control_msg($msg);
        }
        
        if (array_key_exists($rodada_copa, $this->rodadas_cadastradas)) {
            $data_inicio = new DateTime($this->rodadas_cadastradas[$rodada_copa]['inicio']);
            $hoje = new DateTime();
            if ($hoje > $data_inicio) {
                $msg = "Não foi possível entrar na copa :(, ela começou " . $data_inicio->format('d/m H:i');
                $this->control_msg($msg);
            }
        } else {
            $msg = "Não existe copa disponível no momento.";
            $this->control_msg($msg);
        }
        
        $inscrito = $this->Copa_model->verifica_inscrito($id_copa, $id_liga, $rodada_copa, $this->usuario_logado['id']);
        if ($inscrito) {
            $msg = "Você já está participando dessa Copa. Boa sorte!";
            $this->control_msg($msg);
        }
        
        $this->inscricao_copa($id_copa, $id_liga, $rodada_copa);
    }

    /**
     * Depois a copa e a rodada estão ok, ultima etapa é pegar a vaga e também ver se está apto para participar.
     * 
     * @used Portal::rodada_copas               Pega copa para validar a rodada e se a data permite.
     * @uses Copa_model::verifica_vaga          Pega a vaga da copa. Se tiver 16 +1 é por que está cheio.
     * @uses Copa_model::inscricao_copa         Se existe vaga e está apto para participar, inscreve ele na copa.
     * @uses adm_lib::classif_geral             Pega top 32 do bolao de cada categoria para ver se ta apto.
     * @uses Copa_model::verifica_inscrito      Verifica se o usuário já se inscreveu na copa.
     * @uses Portal::usuario_logado             Pega o usuário logado para verificar se está no top 32 de cada categoria
     * @param String $msg                       Uma palavra chave para mostrar a mensagem
     * @return void
     */
    private function inscricao_copa($id_copa, $id_liga, $rodada_copa) {
        $vaga = $this->Copa_model->verifica_vaga($id_copa, $id_liga, $rodada_copa);
        $apto = false;
        
        if ($vaga == 17) {
            $msg = "Desculpe, não existe mais vagas para essa copa :S. Tente se inscrever antes que todo mundo na próxima.";
            $this->control_msg($msg);
        }

        if ($id_liga) {
            $this->Copa_model->inscricao_copa($id_copa, $id_liga, $rodada_copa, $vaga, $this->usuario_logado['id']);
            $msg = "Você se cadastrou na Copa da Liga com sucesso! Foram descontados 3 mangos de você. Boa sorte.";
            $this->control_msg($msg);
        }

        if ($id_copa == 4) {
            $classif = $this->adm_lib->classif_geral("geral");
            $posicao = array_search($this->usuario_logado['id'], array_column($classif, 'id'));
            if ($posicao && $posicao < 32) {
                $apto = true;
                $msg = "Inscrição nos Lendários realizada com sucesso! Você está entre os melhores, boa sorte! Foram descontados 5 mangos pela inscrição.";
            } else {
                $msg = "Sua posição na classificaçao é " . ($posicao + 1) . ". Apenas os 32 primeiros do bolão podem participar.";
            }
        } else if ($id_copa == 3) {
            $classif = $this->adm_lib->classif_geral("desafios");
            $posicao = array_search($this->usuario_logado['id'], array_column($classif, 'id'));
            if ($posicao && $posicao < 32) {
                $apto = true;
                $msg = "Inscrição nos Desafiantes realizada com sucesso! Você está entre os melhores desafiadores, boa sorte! Foram descontados 5 mangos pela inscrição.";
            } else {
                $msg = "Sua posição na classificaçao de desafios é " . ($posicao + 1) . ". Apenas os 32 primeiros podem participar.";
            }
        } else if ($id_copa == 2) {
            $classif = $this->adm_lib->classif_geral("mangos");
            $posicao = array_search($this->usuario_logado['id'], array_column($classif, 'id'));
            if ($posicao && $posicao < 32) {
                $apto = true;
                $msg = "Inscrição nos Capitalistas realizada com sucesso! Você está entre os mais ricos, boa sorte! Foram descontados 5 mangos pela inscrição.";
            } else {
                $msg = "Sua posição na classificaçao de mangos é " . ($posicao + 1) . ". Apenas os 32 primeiros podem participar.";
            }
        }

        if ($apto) {
            $this->Copa_model->inscricao_copa($id_copa, $id_liga, $rodada_copa, $vaga, $this->usuario_logado['id']);
        }

        $this->control_msg($msg);
    }

}
