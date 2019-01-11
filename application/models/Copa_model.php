<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Carrega a class Copa_model
 * 
 * Inscricoes, atualizacoes e dados da copa
 */
class Copa_model extends CI_Model {

    /**
     * Carregará a conexão com o banco
     * 
     * @var array
     */
    private $con;
    
    /**
     * As 4 copas estao aqui e é recebida pelo Adm_lib.
     * @var array 
     */
    private $copas;

    /**
     * Carrega o ConnectionFactory para poder utilizar o banco de dados
     * 
     * @uses ConnectionFactory::getConnection()     Irá pegar o objeto de conexão e salvar no Gerencia_model::con
     * @return void
     */
    public function __construct() {
        parent::__construct();

        $this->load->library('ConnectionFactory');
        $this->load->library('Adm_lib');
        
        $this->con = $this->connectionfactory->getConnection();
        $this->copas= $this->adm_lib->copas();
    }

    /**
     * Pega todas as participaçoes das copas oficiais, total inscritos e todos titulos se houver.
     * 
     * @used-by Adm_lib::todos_dados_usuarios()           Pega o saldo da copa e somará com os mangos.
     * @param int $id_usuario
     * @return array|bool
     */
    public function total_copas_por_id($id_usuario) {
        $sql = "SELECT DISTINCT cac_oitavas AS id, cac_id_copa AS copa, cac_id_liga AS liga, cac_rodada AS rodada, "
                . "NULL AS nome, "
                . "(SELECT DISTINCT cac_quartas FROM cac_cadastrar_copas WHERE cac_id_copa= copa AND cac_id_liga IS NULL AND cac_rodada= rodada AND cac_quartas= id) AS quartas, "
                . "(SELECT DISTINCT cac_semi FROM cac_cadastrar_copas WHERE cac_id_copa= copa AND cac_id_liga IS NULL AND cac_rodada= rodada AND cac_semi= id) AS semi, "
                . "(SELECT DISTINCT cac_final FROM cac_cadastrar_copas WHERE cac_id_copa= copa AND cac_id_liga IS NULL AND cac_rodada= rodada AND cac_final= id) AS final, "
                . "(SELECT DISTINCT cac_campeao FROM cac_cadastrar_copas WHERE cac_id_copa= copa AND cac_id_liga IS NULL AND cac_rodada= rodada AND cac_campeao= id) AS campeao, "
                . "(SELECT DISTINCT cac_campeao FROM cac_cadastrar_copas WHERE cac_id_copa= copa AND cac_id_liga IS NULL AND cac_rodada= rodada AND cac_campeao IS NOT NULL) AS campeao_copa, "
                . "(SELECT COUNT(DISTINCT cac_oitavas) FROM cac_cadastrar_copas WHERE cac_id_copa= copa AND cac_id_liga IS NULL AND cac_rodada= rodada) AS inscritos, "
                . "(SELECT lig_nome FROM lig_ligas WHERE lig_id_liga = liga) AS nome_liga, "
                . "(SELECT DISTINCT cac_quartas FROM cac_cadastrar_copas WHERE cac_id_copa= copa AND cac_id_liga= liga AND cac_rodada= rodada AND cac_quartas= id) AS quartas_liga, "
                . "(SELECT DISTINCT cac_semi FROM cac_cadastrar_copas WHERE cac_id_copa= copa AND cac_id_liga= liga AND cac_rodada= rodada AND cac_semi= id) AS semi_liga, "
                . "(SELECT DISTINCT cac_final FROM cac_cadastrar_copas WHERE cac_id_copa= copa AND cac_id_liga= liga AND cac_rodada= rodada AND cac_final= id) AS final_liga, "
                . "(SELECT DISTINCT cac_campeao FROM cac_cadastrar_copas WHERE cac_id_copa= copa AND cac_id_liga= liga AND cac_rodada= rodada AND cac_campeao= id) AS campeao_liga, "
                . "(SELECT DISTINCT cac_campeao FROM cac_cadastrar_copas WHERE cac_id_copa= copa AND cac_id_liga= liga AND cac_rodada= rodada AND cac_campeao IS NOT NULL) AS campeao_copa_liga, "
                . "(SELECT COUNT(DISTINCT cac_oitavas) FROM cac_cadastrar_copas WHERE cac_id_copa= copa AND cac_id_liga= liga AND cac_rodada= rodada) AS inscritos_liga "
                . "FROM cac_cadastrar_copas WHERE cac_oitavas= :id AND YEAR(cac_created)= :year";
        $stmt = $this->con->prepare($sql);
        $stmt->bindValue(':id', $id_usuario);
        $stmt->bindValue(':year', date('Y'));
        $stmt->execute();

        $dados_copa = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt = null;
        
        foreach ($this->copas as $key => $value) {
            $total_participacao[$key]= 0;
            $total_titulos[$key]= 0;
        }
        $total_participacao['total']= 0;
        $total_titulos['total']= 0;
        
        if ($dados_copa) {
            foreach ($dados_copa AS $key => $value) {
                $copa[$key]['id'] = $value['id'];
                $copa[$key]['copa'] = $value['copa'];
                $copa[$key]['liga'] = $value['liga'];
                $copa[$key]['nome_copa']= ($value['liga']) ? $value['nome_liga'] : $this->copas[$value['copa']]['nome'];
                $copa[$key]['rodada'] = $value['rodada'];
                if ($value['liga']) {
                    $copa[$key]['oitavas'] = $value['id'];
                    $copa[$key]['quartas'] = $value['quartas_liga'];
                    $copa[$key]['semi'] = $value['semi_liga'];
                    $copa[$key]['final'] = $value['final_liga'];
                    $copa[$key]['campeao'] = $value['campeao_liga'];
                    $copa[$key]['campeao_copa'] = $value['campeao_copa_liga'];
                    $copa[$key]['inscritos'] = $value['inscritos_liga'];
                    $copa[$key]['premiacao'] = $value['inscritos_liga'] * $this->copas[$value['copa']]['entrada'];
                    $copa[$key]['saldo'] = ($value['campeao_copa_liga']) ? $value['inscritos_liga'] * $this->copas[$value['copa']]['entrada'] - $this->copas[$value['copa']]['entrada'] : -$this->copas[$value['copa']]['entrada'];
                    $total_participacao[$value['copa']]++;
                    $total_participacao['total']++;
                    if($value['campeao_liga']){
                        $total_titulos[$value['copa']]++;
                        $total_titulos['total']++;
                    }
                } else {
                    $copa[$key]['oitavas'] = $value['id'];
                    $copa[$key]['quartas'] = $value['quartas'];
                    $copa[$key]['semi'] = $value['semi'];
                    $copa[$key]['final'] = $value['final'];
                    $copa[$key]['campeao'] = $value['campeao'];
                    $copa[$key]['campeao_copa'] = $value['campeao_copa'];
                    $copa[$key]['inscritos'] = $value['inscritos'];
                    $copa[$key]['premiacao'] = $value['inscritos'] * $this->copas[$value['copa']]['entrada'];
                    $copa[$key]['saldo'] = ($value['campeao']) ? $value['inscritos'] * $this->copas[$value['copa']]['entrada'] - $this->copas[$value['copa']]['entrada'] : -$this->copas[$value['copa']]['entrada'];
                    $total_participacao[$value['copa']]++;
                    $total_participacao['total']++;
                    if($value['campeao']){
                        $total_titulos[$value['copa']]++;
                        $total_titulos['total']++;
                    }
                }
            }
            $copa['total_participacao']= $total_participacao;
            $copa['total_titulos']= $total_titulos;
        } else {
            return false;
        }
        
        return $copa;
    }

    /**
     * Trás todas as copas dos usuarios e com o saldo ja calculado. A consulta trará tudo da rodada 1 até a rodada atual -1
     * 
     * @used-by Adm_lib::classif_geral()            Pega os dados da copa para somar na classificacao
     * @param type $rodada
     * @return type
     */
    public function total_copas($rodada) {
        $sql = "SELECT DISTINCT cac_oitavas AS id, cac_id_copa AS copa, cac_id_liga AS liga, cac_rodada AS rodada, "
                . "(SELECT DISTINCT cac_quartas FROM cac_cadastrar_copas WHERE cac_id_copa= copa AND cac_id_liga IS NULL AND cac_rodada= rodada AND cac_quartas= id) AS quartas, "
                . "(SELECT DISTINCT cac_semi FROM cac_cadastrar_copas WHERE cac_id_copa= copa AND cac_id_liga IS NULL AND cac_rodada= rodada AND cac_semi= id) AS semi, "
                . "(SELECT DISTINCT cac_final FROM cac_cadastrar_copas WHERE cac_id_copa= copa AND cac_id_liga IS NULL AND cac_rodada= rodada AND cac_final= id) AS final, "
                . "(SELECT DISTINCT cac_campeao FROM cac_cadastrar_copas WHERE cac_id_copa= copa AND cac_id_liga IS NULL AND cac_rodada= rodada AND cac_campeao= id) AS campeao, "
                . "(SELECT DISTINCT cac_campeao FROM cac_cadastrar_copas WHERE cac_id_copa= copa AND cac_id_liga IS NULL AND cac_rodada= rodada AND cac_campeao IS NOT NULL) AS campeao_copa, "
                . "(SELECT COUNT(DISTINCT cac_oitavas) FROM cac_cadastrar_copas WHERE cac_id_copa= copa AND cac_id_liga IS NULL AND cac_rodada= rodada) AS inscritos, "
                . "(SELECT DISTINCT cac_quartas FROM cac_cadastrar_copas WHERE cac_id_copa= copa AND cac_id_liga= liga AND cac_rodada= rodada AND cac_quartas= id) AS quartas_liga, "
                . "(SELECT DISTINCT cac_semi FROM cac_cadastrar_copas WHERE cac_id_copa= copa AND cac_id_liga= liga AND cac_rodada= rodada AND cac_semi= id) AS semi_liga, "
                . "(SELECT DISTINCT cac_final FROM cac_cadastrar_copas WHERE cac_id_copa= copa AND cac_id_liga= liga AND cac_rodada= rodada AND cac_final= id) AS final_liga, "
                . "(SELECT DISTINCT cac_campeao FROM cac_cadastrar_copas WHERE cac_id_copa= copa AND cac_id_liga= liga AND cac_rodada= rodada AND cac_campeao= id) AS campeao_liga, "
                . "(SELECT DISTINCT cac_campeao FROM cac_cadastrar_copas WHERE cac_id_copa= copa AND cac_id_liga= liga AND cac_rodada= rodada AND cac_campeao IS NOT NULL) AS campeao_copa_liga, "
                . "(SELECT COUNT(DISTINCT cac_oitavas) FROM cac_cadastrar_copas WHERE cac_id_copa= copa AND cac_id_liga= liga AND cac_rodada= rodada) AS inscritos_liga "
                . "FROM cac_cadastrar_copas WHERE cac_rodada< :rodada AND YEAR(cac_created)= :year";
        $stmt = $this->con->prepare($sql);
        $stmt->bindValue(':rodada', $rodada);
        $stmt->bindValue(':year', date('Y'));
        $stmt->execute();

        $dados_copa = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt = null;

        if (!$dados_copa) {
            return array();
        }

        foreach ($dados_copa AS $key => $value) {
            $copa[$value['id']][$key]['id'] = $value['id'];
            $copa[$value['id']][$key]['copa'] = $value['copa'];
            $copa[$value['id']][$key]['liga'] = $value['liga'];
            $copa[$value['id']][$key]['rodada'] = $value['rodada'];
            if ($value['liga']) {
                $copa[$value['id']][$key]['quartas'] = $value['quartas_liga'];
                $copa[$value['id']][$key]['semi'] = $value['semi_liga'];
                $copa[$value['id']][$key]['final'] = $value['final_liga'];
                $copa[$value['id']][$key]['campeao'] = $value['campeao_liga'];
                $copa[$value['id']][$key]['campeao_copa'] = $value['campeao_copa_liga'];
                $copa[$value['id']][$key]['inscritos'] = $value['inscritos_liga'];
                $copa[$value['id']][$key]['saldo'] = ($value['campeao_copa_liga']) ? $value['inscritos_liga'] * $this->copas[$value['copa']]['entrada'] - $this->copas[$value['copa']]['entrada'] : -$this->copas[$value['copa']]['entrada'];
            } else {
                $copa[$value['id']][$key]['quartas'] = $value['quartas'];
                $copa[$value['id']][$key]['semi'] = $value['semi'];
                $copa[$value['id']][$key]['final'] = $value['final'];
                $copa[$value['id']][$key]['campeao'] = $value['campeao'];
                $copa[$value['id']][$key]['campeao_copa'] = $value['campeao_copa'];
                $copa[$value['id']][$key]['inscritos'] = $value['inscritos'];
                $copa[$value['id']][$key]['saldo'] = ($value['campeao']) ? $value['inscritos'] * $this->copas[$value['copa']]['entrada'] - $this->copas[$value['copa']]['entrada'] : -$this->copas[$value['copa']]['entrada'];
            }
        }

        return $copa;
    }

    /**
     * Vai consultar se o usuário participa da copa que ele está se inscrevendo. Caso exista, retorna true.
     * 
     * @used-by Copa::rodada_copas()           Verifica se o usuário já se inscreveu na copa.
     * @param type $id_copa                    ID da copa
     * @param type $id_liga                    Se houver, ID liga
     * @param type $rodada_copa                Rodada copa
     * @param type $id_usuario                 Usuario
     * @return bool
     */
    public function verifica_inscrito($id_copa, $id_liga, $rodada_copa, $id_usuario) {
        if (!$id_liga) {
            $sql = "SELECT cac_oitavas FROM cac_cadastrar_copas WHERE cac_id_copa= :id_copa AND cac_rodada= :rodada AND cac_oitavas= :id_usuario AND YEAR(cac_created)= :year";
        } else {
            $sql = "SELECT cac_oitavas FROM cac_cadastrar_copas WHERE cac_id_copa= :id_copa AND cac_id_liga= :id_liga AND cac_rodada= :rodada AND cac_oitavas= :id_usuario AND YEAR(cac_created)= :year";
        }

        $stmt = $this->con->prepare($sql);
        $stmt->bindValue(":id_copa", $id_copa);
        if ($id_liga) {
            $stmt->bindValue(":id_liga", $id_liga);
        }
        $stmt->bindValue(":rodada", $rodada_copa);
        $stmt->bindValue(":id_usuario", $id_usuario);
        $stmt->bindValue(":year", date('Y'));
        $stmt->execute();

        $vagas = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt = null;

        return $vagas;
    }

    /**
     * Pega a vaga da copa. Se tiver 16 é por que está cheio.
     * 
     * @used-by Copa::inscricao_copa()      Pega a vaga da copa. Se tiver 16 +1 é por que está cheio.
     * @used-by Copa::monta_copa()          Pega o numero de inscritos para somar a premiaçao
     * @param type $id_copa                 ID da copa
     * @param type $id_liga                 Se houver, ID liga
     * @param type $rodada_copa             Rodada da copa
     * @return int
     */
    public function verifica_vaga($id_copa, $id_liga, $rodada_copa) {
        if (!$id_liga) {
            $sql = "SELECT COUNT(cac_oitavas) AS posicao FROM cac_cadastrar_copas WHERE cac_id_copa= :id_copa AND cac_rodada= :rodada AND cac_quartas IS NULL AND YEAR(cac_created)= :year";
        } else {
            $sql = "SELECT COUNT(cac_oitavas) AS posicao FROM cac_cadastrar_copas WHERE cac_id_copa= :id_copa AND cac_id_liga= :id_liga AND cac_rodada= :rodada AND cac_quartas IS NULL AND YEAR(cac_created)= :year";
        }

        $stmt = $this->con->prepare($sql);
        $stmt->bindValue(":id_copa", $id_copa);
        if ($id_liga) {
            $stmt->bindValue(":id_liga", $id_liga);
        }
        $stmt->bindValue(":rodada", $rodada_copa);
        $stmt->bindValue(":year", date('Y'));
        $stmt->execute();

        $vagas = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt = null;

        return $vagas['posicao'] + 1;
    }

    /**
     * Verifica se o usuário já se inscreveu na copa.
     * 
     * @used-by Copa::inscricao_copa()          Se existe vaga e está apto para participar, inscreve ele na copa.
     * @param type $id_copa                     ID copa
     * @param type $id_liga                     Se houver, ID liga
     * @param type $rodada_copa                 Rodada da cp[a
     * @param type $vaga                        A posiçao que será cadastrado
     * @param type $id_usuario                  Usuario
     */
    public function inscricao_copa($id_copa, $id_liga, $rodada_copa, $vaga, $id_usuario) {
        $sql = "INSERT INTO cac_cadastrar_copas (cac_id_copa, cac_id_liga, cac_rodada, cac_posicao, cac_oitavas) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->con->prepare($sql);
        $stmt->bindValue(1, $id_copa);
        $stmt->bindValue(2, $id_liga);
        $stmt->bindValue(3, $rodada_copa);
        $stmt->bindValue(4, $vaga);
        $stmt->bindValue(5, $id_usuario);
        $stmt->execute();

        $stmt = null;
    }

    /**
     * Irá receber os dados da copa, a fase desejada e o objeto da Classificacao_model para pegar a pontuaçao da rodada.
     * Obs: É informado a próxima fase por que sem ele traria todo mundo da quartas mesmo solicitando final
     * 
     * @used-by Copa::monta_copa()                                      Solicita os participantes com os parametros validados.
     * @uses Classificacao_model::consulta_classif_user_rodada()        Informa o ID do usuario e rodada para pegar a pontuaçao solicitado da rodada.
     * @param Obj $classificacao_model          Trás o objeto do classificacao_model
     * @param int $id_copa
     * @param int $id_liga
     * @param int $rodada                       Rodada da copa
     * @param int  $rodada_pontos               Rodada para pegar as pontuaçoes.
     * @param String $fase
     * @param String $prox_fase
     * @return array|bool
     */
    public function tras_partic($classificacao_model, $id_copa, $id_liga, $rodada, $rodada_pontos, $fase, $prox_fase = null) {
        if ($id_liga) {
            $sql = "SELECT cac_id_cadastro_copa, cac_posicao, cac_" . $fase . " FROM cac_cadastrar_copas "
                    . "WHERE cac_id_copa= :id_copa AND cac_id_liga= :id_liga AND cac_rodada= :rodada AND cac_" . $fase . " IS NOT NULL AND cac_" . $prox_fase . " IS NULL AND YEAR(cac_created)= :year";
        } else {
            $sql = "SELECT cac_id_cadastro_copa, cac_posicao, cac_" . $fase . " FROM cac_cadastrar_copas "
                    . "WHERE cac_id_copa= :id_copa AND cac_rodada= :rodada AND cac_" . $fase . " IS NOT NULL AND cac_" . $prox_fase . " IS NULL AND YEAR(cac_created)= :year";
        }

        if ($fase == 'campeao' && $id_liga) {
            $sql = "SELECT cac_id_cadastro_copa, cac_posicao, cac_" . $fase . " FROM cac_cadastrar_copas "
                    . "WHERE cac_id_copa= :id_copa AND cac_id_liga= :id_liga AND cac_rodada= :rodada AND cac_" . $fase . " IS NOT NULL AND YEAR(cac_created)= :year";
        } else if ($fase == 'campeao' && !$id_liga) {
            $sql = "SELECT cac_id_cadastro_copa, cac_posicao, cac_" . $fase . " FROM cac_cadastrar_copas "
                    . "WHERE cac_id_copa= :id_copa AND cac_rodada= :rodada AND cac_" . $fase . " IS NOT NULL AND YEAR(cac_created)= :year";
        }

        $stmt = $this->con->prepare($sql);
        $stmt->BindValue(':id_copa', $id_copa);
        if ($id_liga) {
            $stmt->BindValue(':id_liga', $id_liga);
        }
        $stmt->BindValue(':rodada', $rodada);
        $stmt->BindValue(':year', date('Y'));
        $stmt->execute();

        $pega= $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt= null;
        
        if($pega){
            foreach($pega AS $key=>$value){
                $partic[$key+1]['cac_posicao']= $value['cac_posicao'];
                $partic[$key+1]['cac_'.$fase]= $value['cac_'.$fase];
                $partic[$key+1]['pontos']= $classificacao_model->consulta_classif_user_rodada($value['cac_'.$fase], $rodada_pontos);
            }

            return $partic;
        } else{
            return false;
        }
    }

    public function inscricao_copa2($id_copa, $id_liga, $rodada_copa, $vaga, $oitavas, $quartas = null, $semi = null, $final = null, $campeao = null) {
        $sql = "INSERT INTO cac_cadastrar_copas (cac_id_copa, cac_id_liga, cac_rodada, cac_posicao, cac_oitavas, cac_quartas, cac_semi, cac_final, cac_campeao) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->con->prepare($sql);
        $stmt->bindValue(1, $id_copa);
        $stmt->bindValue(2, $id_liga);
        $stmt->bindValue(3, $rodada_copa);
        $stmt->bindValue(4, $vaga);
        $stmt->bindValue(5, $oitavas);
        $stmt->bindValue(6, $quartas);
        $stmt->bindValue(7, $semi);
        $stmt->bindValue(8, $final);
        $stmt->bindValue(9, $campeao);
        $stmt->execute();

        $stmt = null;
    }

}
