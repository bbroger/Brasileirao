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
     * @var array
     */
    private $usuario_logado;
    
    /**
     * Copas do usuário vindo do Adm_lib.
     * 
     * @var array
     */
    private $todas_copas_usuario;

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
     * As 4 copas estao aqui e é recebida pelo Adm_lib.
     * 
     * @var array 
     */
    private $copas;

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
     * @uses Adm_lib::total_mangos_usuarios()     Tras total de mangos do usuario para ver se pode inscrever na copa
     * @uses Adm_lib::rodada_atual()              Nao pode cadastrar em copas se nao tiver rodada.
     * @uses Adm_lib::todos_dados_usuarios()      Recebe todas as copas do usuário
     * @return void
     */
    public function __construct() {
        parent::__construct();

        $this->load->model('Copa_model');
        $this->load->library('Adm_lib');
        $this->load->model('Gerencia_model');
        $this->load->model('Liga_model');
        
        $this->rodadas_cadastradas = $this->Gerencia_model->rodadas_cadastradas();
        $this->rodada_atual = $this->adm_lib->rodada_atual();
        $this->copas = $this->adm_lib->copas();
        $this->usuario_logado = $this->adm_lib->usuario_logado;
        $this->todas_copas_usuario= $this->adm_lib->todos_dados_usuarios($this->usuario_logado['id'], array('copas', 'ligas'));
        $this->mangos_total = $this->adm_lib->total_mangos_usuario($this->usuario_logado['id']);
    }

    /**
     * Irá receber uma mensagem e redirecionar para o portal
     * 
     * @used-by Copa::verifica_copas()          Recebe e valida ID da copa e ID da liga.
     * @used-by Copa::rodada_copas()            Valida a rodada e a data para cadastrar
     * @used-by Copa::inscricao_copa()          Ve o numero de vagas e se ta apto para se inscrever.
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
     * @uses Copa::rodada()    Para carregar a view
     * @return void
     */
    public function index() {
        $this->rodada();
    }

    /**
     * Esse metodo irá montar a view passando parametros.
     * 
     * @uses Copa::rodada_atual               Rodada atual do bolao
     * @uses Copa::usuario_logado             Se existe um usuario logado
     * @param String $msg                     Uma palavra chave para mostrar a mensagem
     * @return void
     */
    public function rodada($recebe_rodada = 0, $recebe_copa = 0, $recebe_liga = 0, $msg = null) {
        $dados = array(
            "rodada" => $recebe_rodada,
            "copa" => $recebe_copa,
            "liga" => $recebe_liga,
            "usuario_logado" => $this->usuario_logado,
            "msg" => $msg,
            "rodada_atual" => $this->rodada_atual['rodada'],
            "copas"=> $this->todas_copas_usuario['copas'],
            "rodadas_cadastradas"=> $this->rodadas_cadastradas,
            "id_copa"=> $this->copas,
            "ligas"=> $this->todas_copas_usuario['ligas']
        );

        $this->load->view('head', $dados);
        $this->load->view('copa');
    }

    /**
     * Quando o usuário for se inscrever na copa, esse metodo confere qual é a copa e qual a liga.
     * 
     * @uses Copa::rodada_atual               Rodada atual do bolao para se inscrever nas copas
     * @uses Adm_lib::copas()                 Tem as copas, ids, preços entre outros....
     * @uses Copa::mangos_total               Para entrar na copa tem preço, por isso confere os mangos para se inscrever.
     * @uses Liga_model::verifica_liga()      Consulta o ID da liga para ver se existe ou se está ativo.
     * @uses Liga_model::verifica_user_liga() Verifica se o usuário realmente está na liga que foi submetido.
     * @uses Copa::rodadas_copas()            Depois que verificou copas e ligas, aplicar as regras de negocio da copa
     * @uses Copa::usuario_logado             Pega o usuário logado para verificar se esse usuario participa da liga submetida
     * @uses Copa::control_msg()              Redireciona para o portal com uma mensagem.
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
            $msg = "Você não tem mangos suficientes para entrar na Copa. A entrada custa " . $this->copas[$id_copa]['entrada'] . " e você possiu $this->mangos_total";
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
     * @used-by Copa::verifica_copas()          Recebe o ID copa e liga para validar.
     * @uses Copa::rodada_atual                 Rodada atual para calcular e pegar a rodada das copas.
     * @uses Adm_lib::copas()                   Pega o inicio e termino da copa submetida
     * @uses Copa::rodadas_cadastradas          Pega as rodadas cadastradas para ver se a rodada da copa existe e pega a data inicio.
     * @uses Copa_model::verifica_inscrito()    Verifica se o usuário já se inscreveu na copa.
     * @uses Copa::inscricao_copa()             Depois que pegou a copa e a rodada, na proxima etapa pegará a vaga e se está apto para inscrever.
     * @uses Copa::control_msg()                Redireciona para o portal com uma mensagem.
     * @param int $id_copa
     * @param int $id_liga
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
     * @used-by Copa::rodada_copas              Pega copa para validar a rodada e se a data permite.
     * @uses Copa_model::verifica_vaga()        Pega a vaga da copa. Se tiver 16 +1 é por que está cheio.
     * @uses Copa_model::inscricao_copa()       Se existe vaga e está apto para participar, inscreve ele na copa.
     * @uses Adm_lib::classif_geral()           Pega top 32 do bolao de cada categoria para ver se ta apto.
     * @uses Copa::usuario_logado               Pega o usuário logado para verificar se está no top 32 de cada categoria
     * @uses Copa::control_msg()                Redireciona para o portal com uma mensagem.
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

    //AJAX

    /**
     * Irá montar a copa trazendo os participantes com base na copa e rodada. Caso algum dado esteja inválido por padrao retornará a primeira copa capitalista.
     * 
     * @uses Adm_lib::confere_rodada()          Verifica se a rodada é valido para consultar os participantes
     * @uses Adm_lib::copas()                   Verifica se o ID da copa recebido é valido com as copas existentes
     * @uses Copa::monta_copa()                 Depois que validou a rodada e a copa, irá consultar os participantes com esses parametros.
     * @param int $rodada                       Recebe a rodada da copa
     * @param int $id_copa                      Recebe o ID da copa
     * @param int $id_liga                      Recebe o ID da liga
     * @return json
     */
    public function recebe_dados_copa($rodada = null, $id_copa = null, $id_liga = null) {
        $confere_rodada = $this->adm_lib->confere_rodada($rodada);

        if (!is_numeric($id_copa) || $id_copa < 1 || $id_copa > 4) {
            $id_copa = 2;
        }
        
        $liga= null;
        if ($id_copa == 1) {
            if ($id_liga == null || !is_numeric($id_liga) || $id_liga < 1) {
                $id_copa = 2;
                $id_liga = null;
            }
            $liga= $this->Liga_model->verifica_liga($id_liga);
            if(!$liga){
                $id_copa = 2;
                $id_liga = null;
            }
        }

        $inicio_copa = $this->copas[$id_copa]['primeiro'];
        $termino_copa = $this->copas[$id_copa]['ultimo'];
        $rodada_copa = false;

        for ($i = $inicio_copa; $i <= $termino_copa; $i += 4) {
            if ($confere_rodada['rodada'] == $i) {
                $rodada_copa = true;
                break;
            }
        }

        if (!$rodada_copa) {
            $id_copa= 2;
            $confere_rodada['rodada'] = 4;
            $id_liga= null;
        }

        $participantes = $this->monta_copa($id_copa, $id_liga, $liga, $confere_rodada['rodada']);
        echo json_encode($participantes);
    }

    /**
     * Depois que validou a rodada e a copa, irá trazer os participantes já com a pontuaçao da rodada desejada.
     * 
     * @used-by Copa::recebe_dados_copa()             Depois que validou chama essa funçao para trazer os dados.
     * @uses Copa_model::tras_partic()                Irá mandar os dados da copa e retornará todos participantes da fase desejada juntamente com a pontuaçao da rodada.
     * @uses Adm_lib::todos_dados_usuarios()          Irá consultar os dados dos participantes para pegar apelido, imagem do perfil e titulos das copas.
     * @uses Copa_model::verifica_vaga()              Pega o número de inscritos para somar a premiaçao
     * @uses Adm_lib::confere_rodada()                Verifica se a rodada é valido para consultar a data inicio da rodada e mostrar na copa
     * @uses Copa::rodadas_cadastradas                Consulta a rodada para pegar a data inicio String
     * @param int $id_copa
     * @param int $id_liga
     * @param int $rodada
     * @return array
     */
    public function monta_copa($id_copa, $id_liga, $liga, $rodada) {
        $this->load->model('Classificacao_model');
        $oitavas = $this->Copa_model->tras_partic($this->Classificacao_model, $id_copa, $id_liga, $rodada, $rodada, 'oitavas', 'quartas');
        $quartas = $this->Copa_model->tras_partic($this->Classificacao_model, $id_copa, $id_liga, $rodada, $rodada + 1, 'quartas', 'semi');
        $semi = $this->Copa_model->tras_partic($this->Classificacao_model, $id_copa, $id_liga, $rodada, $rodada + 2, 'semi', 'final');
        $final = $this->Copa_model->tras_partic($this->Classificacao_model, $id_copa, $id_liga, $rodada, $rodada + 3, 'final', 'campeao');
        $campeao = $this->Copa_model->tras_partic($this->Classificacao_model, $id_copa, $id_liga, $rodada, $rodada + 4, 'campeao', null);

        if ($oitavas) {
            foreach ($oitavas AS $key => $value) {
                $dados_usuario = $this->adm_lib->todos_dados_usuarios($value['cac_oitavas'], array('usuario', 'copas'));
                $oitavas[$key]['copas'] = $dados_usuario['copas'];
                $oitavas[$key]['apelido'] = $dados_usuario['usuario']['use_nickname'];
                $oitavas[$key]['img_perfil'] = $dados_usuario['usuario']['use_img_perfil'];
            }
        }

        for ($i = 1; $i <= 16; $i++) {
            if (isset($oitavas[$i])) {
                $participantes['oitavas'][$i]['mostra'] = "<img class='img_perfil_copa' src='" . $oitavas[$i]['img_perfil'] . "'><p class='user_copa'>" . $oitavas[$i]['apelido'] . " " . $oitavas[$i]['pontos']['pontos'] . " pontos | M$ " . $oitavas[$i]['pontos']['lucro'] . "</p>";
            } else {
                $participantes['oitavas'][$i]['mostra'] = '<b>-</b>';
            }

            if (isset($quartas[$i])) {
                $participantes['quartas'][$i]['mostra'] = "<img class='img_perfil_copa' src='" . $oitavas[$quartas[$i]['cac_posicao']]['img_perfil'] . "'><p class='user_copa'>" . $oitavas[$quartas[$i]['cac_posicao']]['apelido'] . " " . $quartas[$i]['pontos']['pontos'] . " pontos | M$ " . $quartas[$i]['pontos']['lucro'] . "</p>";
            } else if ($i <= 8) {
                $participantes['quartas'][$i]['mostra'] = '-';
            }

            if (isset($semi[$i])) {
                $participantes['semi'][$i]['mostra'] = "<img class='img_perfil_copa' src='" . $oitavas[$semi[$i]['cac_posicao']]['img_perfil'] . "'><p class='user_copa'>" . $oitavas[$semi[$i]['cac_posicao']]['apelido'] . " " . $semi[$i]['pontos']['pontos'] . " pontos | M$ " . $semi[$i]['pontos']['lucro'] . "</p>";
                ;
            } else if ($i <= 4) {
                $participantes['semi'][$i]['mostra'] = '-';
            }

            if (isset($final[$i])) {
                $participantes['final'][$i]['mostra'] = "<img class='img_perfil_copa' src='" . $oitavas[$final[$i]['cac_posicao']]['img_perfil'] . "'><p class='user_copa'>" . $oitavas[$final[$i]['cac_posicao']]['apelido'] . " " . $final[$i]['pontos']['pontos'] . " pontos | M$ " . $final[$i]['pontos']['lucro'] . "</p>";
                ;
            } else if ($i <= 2) {
                $participantes['final'][$i]['mostra'] = '-';
            }

            if (isset($campeao[$i])) {
                $participantes['campeao'][$i]['mostra'] = "<img class='img_perfil_copa' src='" . $oitavas[$campeao[$i]['cac_posicao']]['img_perfil'] . "'><p class='user_copa'>" . $oitavas[$campeao[$i]['cac_posicao']]['apelido'];
                ;
            } else if ($i <= 1) {
                $participantes['campeao'][$i]['mostra'] = '-';
            }
        }

        $participantes['inscritos'] = $this->Copa_model->verifica_vaga($id_copa, $id_liga, $rodada) - 1;
        $participantes['premiacao'] = $participantes['inscritos'] * $this->copas[$id_copa]['entrada'];
        $participantes['nome'] = ($id_copa == 1) ? "Copa ".$liga['lig_nome'] : $this->copas[$id_copa]['nome'];
        $participantes['rodada'] = $rodada;
        $confere_rodada = $this->adm_lib->confere_rodada($rodada);
        if($confere_rodada['existe']){
            $participantes['data']= $this->rodadas_cadastradas[$confere_rodada['rodada']]['inicio_string'];
        } else{
            $participantes['data']= "em breve";
        }
        
        return $participantes;
    }

    public function teste() {
        for ($i = 1; $i <= 1; $i++) {
            for ($ii = 1; $ii <= 1; $ii++) {
                if ($i == 1) {
                    $this->Copa_model->inscricao_copa2($i, 1, 1, $ii, $ii, $ii, $ii, $ii);
                } else {
                    $this->Copa_model->inscricao_copa2($i, null, 4, $ii, $ii, $ii, $ii, $ii, $ii);
                }
            }
        }
    }

}
