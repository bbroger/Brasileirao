<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Carrega a class Gerencia_model
 * 
 * Depois que passou na Classe gerencia para aplicar a regra de negócio, aqui irá manipular no banco de dados.
 */
class Gerencia_model extends CI_Model {
    
    /**
     * Carregará a conexão com o banco
     * 
     * @var array
     */
    private $con;
    
    /**
     * Carrega o ConnectionFactory para poder utilizar o banco de dados
     * 
     * @uses ConnectionFactory::getConnection()     Irá pegar o objeto de conexão e salvar no Gerencia_model::con
     * @return void
     */
    public function __construct() {
        parent::__construct();
        
        $this->load->library('ConnectionFactory');
        $this->con = $this->connectionfactory->getConnection();
    }
    
    /**
     * Vai trazer todos os times com a série A. Todo ano terá que fazer alteraçoes nos 20 times.
     * 
     * @used-by Gerencia    Usando no __construct para carregar todos os times
     * @return array
     */
    public function todos_times(){
        $sql= "SELECT * FROM tim_times WHERE tim_serie = 'a'";
        $stmt= $this->con->prepare($sql);
        $stmt->execute();
        
        $tras_times= $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($tras_times as $key=> $value) {
            $times[$value["tim_var"]]['nome']= $value["tim_nome"];
            $times[$value["tim_var"]]['first_color']= $value["tim_first_color"];
            $times[$value["tim_var"]]['second_color']= $value["tim_second_color"];
        }
        
        return $times;
    }
    
    /**
     * Vai consultar a rodada solicitada e trazer todos os dados dela.
     * 
     * @used-by Portal::detalhes_palpites()         Pega os os detalhes da rodada para mostrar compactado no Portal
     * @used-by Gerencia::consultar_rodadas()       Pesquisa a rodada para trazer os detalhes da rodada solicitado
     * @used-by Gerencia::enviar_resultado()        Pesquisa a rodada para pegar a data inicio
     * @param int $rodada                           Rodada para ser consultado
     * @return array|bool
     */
    public function consultar_rodada($rodada){
        $sql= "SELECT * FROM cad_cadastrar_rodadas WHERE cad_rodada= ? AND YEAR(cad_created)= ?";
        $stmt= $this->con->prepare($sql);
        $stmt->bindValue(1, $rodada);
        $stmt->bindValue(2, date('Y'));
        $stmt->execute();
        
        $tras_rodada= $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt= null;
        
        return $tras_rodada;
    }
    
    /**
     * Vai consultar o inicio e fim de cada rodada e também se alguma partida dessa rodada foi adiada.
     * 
     * @used-by Gerencia    Usando no __construct para carregar todas as rodadas cadastradas
     * @used-by Palpites
     * @used-by Adm_lib
     * @return array
     */
    public function rodadas_cadastradas(){
        $sql= "SELECT cad_rodada AS rodada, min(cad_data) AS inicio, max(cad_data) AS fim, "
                . "(SELECT COUNT(cad_adiou) FROM cad_cadastrar_rodadas WHERE cad_rodada = rodada AND cad_adiou= 'sim' AND YEAR(cad_created)= :year) AS adiou FROM cad_cadastrar_rodadas "
                . "WHERE cad_adiou = 'nao' AND YEAR(cad_created)= :year GROUP BY cad_rodada";
        $stmt= $this->con->prepare($sql);
        $stmt->bindValue(':year', date('Y'));
        $stmt->execute();
        
        $tras_rodadas= $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt= null;
        $rodadas = array();
        
        if($tras_rodadas){
            foreach ($tras_rodadas AS $key=>$value){
                $rodadas[$value["rodada"]]["inicio"]= $value["inicio"];
                $data_fim= new DateTime($value['fim']);
                $data_fim->add(new DateInterval("PT2H"));
                $rodadas[$value["rodada"]]["fim"]= $data_fim->format("Y-m-d H:i:s");
                $data_string= new DateTime($value["inicio"]);
                $rodadas[$value["rodada"]]["inicio_string"]= $data_string->format("d/m H:i");
                $rodadas[$value['rodada']]['adiou']= ($value['adiou'] > 0) ? true: false;
            }
        }
        
        return $rodadas;
    }
    
    /**
     * Vai salvar as novas rodadas
     * 
     * @used-by Gerencia::acao_rodada() Salva uma nova rodada
     * @param int       $rodada         Contém o número da rodada para ser cadastrado
     * @param array     $dados_rodada   Contém todos os detalhes da rodada
     * @return void
     */
    public function salvar_nova_rodada($rodada, $dados_rodada, $id_usuario){
        $sql= "INSERT INTO cad_cadastrar_rodadas "
                . "(cad_rodada, cad_partida, cad_time_mandante, cad_time_visitante, cad_time_mandante_var, cad_time_visitante_var, cad_local, cad_data, cad_adiou, cad_user_id) "
                . "VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt= $this->con->prepare($sql);
        foreach($dados_rodada AS $key=>$value){
            $stmt->bindValue(1, $rodada);
            $stmt->bindValue(2, $key);
            $stmt->bindValue(3, $value["time_mandante"]);
            $stmt->bindValue(4, $value["time_visitante"]);
            $stmt->bindValue(5, $value["time_mandante_var"]);
            $stmt->bindValue(6, $value["time_visitante_var"]);
            $stmt->bindValue(7, $value["local_partida"]);
            $stmt->bindValue(8, $value["data_partida"]);
            $stmt->bindValue(9, $value["adiou_partida"]);
            $stmt->bindValue(10, $id_usuario);
            $stmt->execute();
        }
        $stmt= null;
    }
    
    /**
     * Vai atualizar as rodadas
     * 
     * @used-by Gerencia::acao_rodada() Atualiza uma rodada
     * @param int       $rodada         Rodada para fazer a alteraçao
     * @param array     $dados_rodada   Contém os novos dados para alterar a rodada.
     * @return void
     */
    public function atualizar_rodada($rodada, $dados_rodada){
        $sql= "UPDATE cad_cadastrar_rodadas SET "
                . "cad_time_mandante= ?, cad_time_visitante= ?, cad_time_mandante_var= ?, cad_time_visitante_var= ?, cad_local= ?, cad_data= ?, cad_adiou= ? "
                . "WHERE cad_rodada= ? AND cad_partida= ? AND YEAR(cad_created)= ?";
        
        $stmt= $this->con->prepare($sql);
        foreach($dados_rodada AS $key=>$value){
            $stmt->bindValue(1, $value["time_mandante"]);
            $stmt->bindValue(2, $value["time_visitante"]);
            $stmt->bindValue(3, $value["time_mandante_var"]);
            $stmt->bindValue(4, $value["time_visitante_var"]);
            $stmt->bindValue(5, $value["local_partida"]);
            $stmt->bindValue(6, $value["data_partida"]);
            $stmt->bindValue(7, $value["adiou_partida"]);
            $stmt->bindValue(8, $rodada);
            $stmt->bindValue(9, $key);
            $stmt->bindValue(10, date('Y'));
            $stmt->execute();
        }
        $stmt= null;
    }
    
    /**
     * Essa funçao irá atualizar a tabela dos palpites as partidas que foram adiadas.
     * 
     * @used-by Gerencia::acao_rodada() Depois que recebeu a atualizaçao da rodada, se adiou alguma partida chamará essa funçao
     * @param type $rodada
     * @param type $partidas_adiadas
     */
    public function adiar_partidas_palpites($rodada, $partidas_adiadas, $adiou){
        if($adiou== 'sim'){
            $sql= "UPDATE pap_palpites SET pap_adiou= :adiou WHERE pap_rodada= :rodada AND pap_partida= :partida AND YEAR(pap_created)= :year";
        } else{
            $sql= "UPDATE pap_palpites SET pap_adiou= :adiou WHERE pap_rodada= :rodada AND YEAR(pap_created)= :year";
        }
        
        $stmt= $this->con->prepare($sql);
        foreach($partidas_adiadas AS $key=>$value){
            $stmt->bindValue('adiou', $adiou);
            $stmt->bindValue(':rodada', $rodada);
            if($adiou== 'sim'){
                $stmt->bindValue(':partida', $key);
            }
            $stmt->bindValue(':year', date('Y'));
            $stmt->execute();
        }
    }
    
    /**
     * Vai salvar os resultados dos palpites. $key será o id do usuario
     * 
     * @used-by Gerencia::calcula_pontos_partida()    Depois que calculos, usará para salvar os pontos dos usuarios
     * @param int   $rodada                           Pega a rodada dos palpites
     * @param int   $partida                          Partida que foi palpitado
     * @param array $palpites                         Contem todos os resultados dos calculos.
     * @return void
     */
    public function salvar_resul_palpites($rodada, $partida, $palpites){
        $sql= "UPDATE pap_palpites SET pap_cc= ?, pap_ct= ?, pap_cf= ?, pap_pontos= ?, pap_lucro= ?, pap_saldo=? "
                . "WHERE pap_id_palpite= ?";
        $stmt= $this->con->prepare($sql);
        foreach ($palpites as $key => $value) {
            $stmt->bindValue(1, $value["cc"]);
            $stmt->bindValue(2, $value["ct"]);
            $stmt->bindValue(3, $value["cf"]);
            $stmt->bindValue(4, $value["pontos"]);
            $stmt->bindValue(5, $value["lucro"]);
            $stmt->bindValue(6, $value["saldo"]);
            $stmt->bindValue(7, $key);
            $stmt->execute();
        }
        $stmt= null;
    }
    
    /**
     * Vai salvar os gols das partidas
     * 
     * @used-by Gerencia::calcula_palpites()    Enviará os gols da partida
     * @param int $rodada                       Rodada para salvar o gol
     * @param int $partida                      Partida que sairam os gols
     * @param int $gol_mandante                 Gol mandante
     * @param int $gol_visitante                Gol visitante
     * @return void
     */
    public function salvar_gols_partida($rodada, $partida, $gol_mandante, $gol_visitante){
        $sql= "UPDATE cad_cadastrar_rodadas SET cad_time_mandante_gol= ?, cad_time_visitante_gol= ? WHERE cad_rodada= ? AND cad_partida= ? AND YEAR(cad_created)= ?";
        $stmt= $this->con->prepare($sql);
        $stmt->bindValue(1, $gol_mandante);
        $stmt->bindValue(2, $gol_visitante);
        $stmt->bindValue(3, $rodada);
        $stmt->bindValue(4, $partida);
        $stmt->bindValue(5, date('Y'));
        $stmt->execute();
        $stmt= null;
    }
}
