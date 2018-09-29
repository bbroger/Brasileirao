<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Carrega a class Desafios_model
 * 
 * Cadastrar, salvar resultado e trazer todos os dados dos desafios individuais e duplas
 */
class Desafios_model extends CI_Model {

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
     * Vai trazer os dados do desafio do usuario
     * 
     * @used-by Adm_lib::todos_dados_user()         Pega os desafios para calcular os mangos do usuario
     * @param int $id
     * @return array
     */
    public function total_dados_desafio_user($id) {
        $sql = "SELECT dei_id_user_desafiador AS id, "
                . "(SELECT COUNT(dei_id_user_desafiador) FROM dei_desafios_individual WHERE dei_id_user_desafiador= id AND dei_status= 'aceito' AND YEAR(dei_created)= :year) AS defr_ace, "
                . "NULL AS 'def_ace', "
                . "(SELECT COUNT(dei_id_user_desafiador) FROM dei_desafios_individual WHERE dei_id_user_desafiador= id AND dei_status= 'pendente' AND YEAR(dei_created)= :year) AS defr_pen, "
                . "NULL AS 'def_pen', "
                . "(SELECT COUNT(dei_vencedor) FROM dei_desafios_individual WHERE dei_vencedor= id AND YEAR(dei_created)= :year) AS venceu "
                . "FROM dei_desafios_individual WHERE dei_id_user_desafiador= :id AND YEAR(dei_created)= :year GROUP BY id "
                . "UNION ALL "
                . "SELECT dei_id_user_desafiado AS id, "
                . "NULL AS 'defr_ace', "
                . "(SELECT COUNT(dei_id_user_desafiado) FROM dei_desafios_individual WHERE dei_id_user_desafiado= id AND dei_status= 'aceito' AND YEAR(dei_created)= :year) AS def_ace, "
                . "NULL AS 'defr_pen', "
                . "(SELECT COUNT(dei_id_user_desafiado) FROM dei_desafios_individual WHERE dei_id_user_desafiado= id AND dei_status= 'pendente' AND YEAR(dei_created)= :year) AS def_pen, "
                . "(SELECT COUNT(dei_vencedor) FROM dei_desafios_individual WHERE dei_vencedor= id AND YEAR(dei_created)= :year) AS venceu "
                . "FROM dei_desafios_individual WHERE dei_id_user_desafiado= :id AND YEAR(dei_created)= :year GROUP BY id";
        $stmt = $this->con->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->bindValue(':year', date('Y'));
        $stmt->execute();
        
        $dados= $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt= null;
        
        foreach ($dados as $key => $value) {
            if(isset($value['defr_ace'])){
                $desafios['defr_aceito']= $value['defr_ace'];
                $desafios['def_aceito']= (isset($desafios['def_aceito'])) ? $desafios['def_aceito']: 0;
                $desafios['defr_pendente']= $value['defr_pen'];
                $desafios['def_pendente']= (isset($desafios['def_pendente'])) ? $desafios['def_pendente']: 0;
                $desafios['venceu']= $value['venceu'];
                $desafios['saldo']= $value['venceu'] * 2 - ($desafios['defr_pendente'] + $desafios['defr_aceito'] + $desafios['def_aceito']);
            } else{
                $desafios['defr_aceito']= (isset($desafios['defr_aceito'])) ? $desafios['defr_aceito']: 0;
                $desafios['def_aceito']= $value['def_ace'];
                $desafios['defr_pendente']= (isset($desafios['defr_pendente'])) ? $desafios['defr_pendente']: 0;
                $desafios['def_pendente']= $value['def_pen'];
                $desafios['venceu']= $value['venceu'];
                $desafios['saldo']= $value['venceu'] * 2 - ($desafios['defr_pendente'] + $desafios['defr_aceito'] + $desafios['def_aceito']);
            }
        }
        
        return $desafios;
    }

    /**
     * Irá receber a decisao. Irá atualizar no banco se aceitou ou recusou, novo ou até mesmo se cancelou
     * 
     * @used-by Desafios::decisao_desafio()           Irá enviar os dados dos desafiantes e a decisao para gravar.
     * @used-by Desafios::cancelar_desafio()          Irá cancelar o desafio e mostrar que foi cancelado sem problemas
     * @param int    $rodada
     * @param int    $id_desafiado
     * @param String $id_desafiador
     * @param String $decisao                               aceito, recusado ou cancelado
     * @return String
     */
    public function decisao_desafio($rodada, $id_desafiado, $id_desafiador, $decisao) {        
        $sql = "UPDATE dei_desafios_individual SET dei_status= ? "
                . "WHERE dei_rodada= ? "
                . "AND ((dei_id_user_desafiador = ? AND dei_id_user_desafiado= ?) "
                . "OR (dei_id_user_desafiador = ? AND dei_id_user_desafiado= ?)) "
                . "AND dei_status= 'pendente' "
                . "AND YEAR(dei_created)= ?";
        $stmt = $this->con->prepare($sql);
        $stmt->bindValue(1, $decisao);
        $stmt->bindValue(2, $rodada);
        $stmt->bindValue(3, $id_desafiador);
        $stmt->bindValue(4, $id_desafiado);
        $stmt->bindValue(5, $id_desafiado);
        $stmt->bindValue(6, $id_desafiador);
        $stmt->bindValue(7, date('Y'));
        $stmt->execute();

        $stmt = null;

        $msg = "O desafio foi $decisao com sucesso!";

        return $msg;
    }

    /**
     * Confere se o apelido existe.
     * 
     * @used-by Desafios::decisao_desafio()                         Irá verificar se existe o adversário. Se sim, retorna o ID
     * @param String $apelido
     * @return int|bool
     */
    public function tras_id_adversario($apelido) {
        $sql = "SELECT use_id_user FROM use_users WHERE use_nickname = ?";
        $stmt = $this->con->prepare($sql);
        $stmt->bindValue(1, $apelido);
        $stmt->execute();

        $id = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt = null;
        return $id;
    }

    /**
     * Confere se já existe o desafio pendente ou aceito.
     * 
     * @used-by Desafios::decisao_desafio()                         Irá verificar se existe o desafio pendente. Se nao existir, não tem o que decidir.
     * @param int $rodada                                           Rodada atual
     * @param int $id_desafiador                                    ID do usuario logado
     * @param int $id_desafiado                                     ID do desafiado
     * @return bool
     */
    public function verifica_existencia_desafio($rodada, $id_desafiador, $id_desafiado) {
        $sql = "SELECT dei_id_user_desafiador AS desafiador, dei_id_user_desafiado AS desafiado FROM dei_desafios_individual "
                . "WHERE dei_rodada= ? "
                . "AND ((dei_id_user_desafiador = ? AND dei_id_user_desafiado= ?) "
                . "OR (dei_id_user_desafiador = ? AND dei_id_user_desafiado= ?)) "
                . "AND (dei_status= 'aceito' OR dei_status= 'pendente') "
                . "AND YEAR(dei_created)= ?";
        $stmt = $this->con->prepare($sql);
        $stmt->bindValue(1, $rodada);
        $stmt->bindValue(2, $id_desafiador);
        $stmt->bindValue(3, $id_desafiado);
        $stmt->bindValue(4, $id_desafiado);
        $stmt->bindValue(5, $id_desafiador);
        $stmt->bindValue(6, date('Y'));
        $stmt->execute();
        
        $dados= $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt = null;
        
        if ($dados) {
            return $dados;
        }
        
        return false;
    }

    /**
     * Quando todas as validaçoes der TRUE, cadastra o novo desafio.
     * 
     * @used-by Desafios::decisao_desafio()                         Depois que validou tudo, registra um novo desafio
     * @param int $rodada                                           Rodada atual para cadastrar o desafio
     * @param int $id_desafiador                                    ID do usuario logado
     * @param int $id_desafiado                                     ID do desafiado
     * @return void
     */
    public function registra_novo_desafio($rodada, $id_desafiador, $adversario) {
        $sql = "INSERT INTO dei_desafios_individual (dei_rodada, dei_id_user_desafiador, dei_id_user_desafiado) VALUES (?, ?, ?)";
        $stmt = $this->con->prepare($sql);
        $stmt->bindValue(1, $rodada);
        $stmt->bindValue(2, $id_desafiador);
        $stmt->bindValue(3, $adversario);
        $stmt->execute();

        $stmt = null;
    }
    
    /**
     * Tras os desafios da rodada atual pro usuario. Se encontrar, pega o adversário e consulta os dados dele no Adm_lib
     * 
     * @used-by Portal::todos_desafios()            Irá pegar os desafios e mostrar ao usuario
     * @uses Desafios_model::pega_adversarios       Depois que achou os adversarios, busca os dados dele.
     * @param int $rodada
     * @param int $id_usuario
     * @return bool|array
     */
    public function todos_desafios_rodada($rodada, $id_usuario) {
        $sql = "SELECT dei_id_user_desafiador AS desafiador, dei_id_user_desafiado AS desafiado, dei_status FROM dei_desafios_individual "
                . "WHERE dei_rodada= ? "
                . "AND (dei_id_user_desafiador = ? OR dei_id_user_desafiado= ?) "
                . "AND (dei_status= 'pendente' OR dei_status= 'aceito') "
                . "AND YEAR(dei_created)= ?";
        $stmt= $this->con->prepare($sql);
        $stmt->bindValue(1, $rodada);
        $stmt->bindValue(2, $id_usuario);
        $stmt->bindValue(3, $id_usuario);
        $stmt->bindValue(4, date('Y'));
        $stmt->execute();

        $dados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt = null;
        
        if (!$dados) {
            return false;
        }
        
        return $this->pega_adversarios($id_usuario, $dados);
    }
    
    /**
     * Consulta os adversários no Adm_lib e mostra quem é o desafiador ou desafiado
     * 
     * @used-by Desafios_model::todos_desafios_rodada()         É responsavel de me dar os ids para consultar os dados do usuario
     * @uses Adm_lib::todos_daos_usuarios()                     Tras os dados do usuario
     * @param int $id_usuario
     * @param int $dados
     * @return array
     */
    private function pega_adversarios($id_usuario, $dados) {
        $this->load->library('Adm_lib');
        
        $desafio[0]['usuario']= $this->adm_lib->todos_dados_usuarios($id_usuario, true);
        foreach ($dados as $key => $value) {
            if ($id_usuario != $value['desafiador']) {
                $desafio[$key+1]['usuario']= $this->adm_lib->todos_dados_usuarios($value['desafiador'], true);
                $desafio[$key+1]['status']= $value['dei_status'];
                $desafio[$key+1]['desafiador']= true;
                $desafio[$key+1]['desafiado']= false;
            } else{
                $desafio[$key+1]['usuario']= $this->adm_lib->todos_dados_usuarios($value['desafiado'], true);
                $desafio[$key+1]['status']= $value['dei_status'];
                $desafio[$key+1]['desafiador']= false;
                $desafio[$key+1]['desafiado']= true;
            }
        }
        
        return $desafio;
    }
    
    public function teste($rodada){
        $sql = "SELECT dei_id_user_desafiador AS id, "
                . "(SELECT COUNT(dei_id_user_desafiador) FROM dei_desafios_individual WHERE dei_rodada< :rodada AND dei_id_user_desafiador= id AND dei_status= 'aceito' AND YEAR(dei_created)= :year) AS defr_ace, "
                . "NULL AS 'def_ace', "
                . "(SELECT COUNT(dei_id_user_desafiador) FROM dei_desafios_individual WHERE dei_rodada< :rodada AND dei_id_user_desafiador= id AND dei_status= 'pendente' AND YEAR(dei_created)= :year) AS defr_pen, "
                . "NULL AS 'def_pen', "
                . "(SELECT COUNT(dei_vencedor) FROM dei_desafios_individual WHERE dei_rodada< :rodada AND dei_vencedor= id AND YEAR(dei_created)= :year) AS venceu "
                . "FROM dei_desafios_individual WHERE dei_rodada< :rodada AND YEAR(dei_created)= :year GROUP BY id "
                . "UNION ALL "
                . "SELECT dei_id_user_desafiado AS id, "
                . "NULL AS 'defr_ace', "
                . "(SELECT COUNT(dei_id_user_desafiado) FROM dei_desafios_individual WHERE dei_rodada< :rodada AND dei_id_user_desafiado= id AND dei_status= 'aceito' AND YEAR(dei_created)= :year) AS def_ace, "
                . "NULL AS 'defr_pen', "
                . "(SELECT COUNT(dei_id_user_desafiado) FROM dei_desafios_individual WHERE dei_rodada< :rodada AND dei_id_user_desafiado= id AND dei_status= 'pendente' AND YEAR(dei_created)= :year) AS def_pen, "
                . "(SELECT COUNT(dei_vencedor) FROM dei_desafios_individual WHERE dei_rodada< :rodada AND dei_vencedor= id AND YEAR(dei_created)= :year) AS venceu "
                . "FROM dei_desafios_individual WHERE dei_rodada< :rodada AND YEAR(dei_created)= :year GROUP BY id";
        $stmt = $this->con->prepare($sql);
        $stmt->bindValue(':rodada', $rodada);
        $stmt->bindValue(':year', date('Y'));
        $stmt->execute();
        
        $dados= $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt= null;
        
        if(!$dados){
            return false;
        }
        
        foreach ($dados as $key => $value) {
            if(isset($value['defr_ace'])){
                $desafios[$value['id']]['defr_aceito']= $value['defr_ace'];
                $desafios[$value['id']]['def_aceito']= (isset($desafios[$value['id']]['def_aceito'])) ? $desafios[$value['id']]['def_aceito']: 0;
                $desafios[$value['id']]['defr_pendente']= $value['defr_pen'];
                $desafios[$value['id']]['def_pendente']= (isset($desafios[$value['id']]['def_pendente'])) ? $desafios[$value['id']]['def_pendente']: 0;
                $desafios[$value['id']]['venceu']= $value['venceu'];
                $desafios[$value['id']]['saldo']= $value['venceu'] * 2 - ($desafios[$value['id']]['defr_aceito'] + $desafios[$value['id']]['def_aceito']);
            } else{
                $desafios[$value['id']]['defr_aceito']= (isset($desafios[$value['id']]['defr_aceito'])) ? $desafios[$value['id']]['defr_aceito']: 0;
                $desafios[$value['id']]['def_aceito']= $value['def_ace'];
                $desafios[$value['id']]['defr_pendente']= (isset($desafios[$value['id']]['defr_pendente'])) ? $desafios[$value['id']]['defr_pendente']: 0;
                $desafios[$value['id']]['def_pendente']= $value['def_pen'];
                $desafios[$value['id']]['venceu']= $value['venceu'];
                $desafios[$value['id']]['saldo']= $value['venceu'] * 2 - ($desafios[$value['id']]['defr_aceito'] + $desafios[$value['id']]['def_aceito']);
            }
        }
        
        return $desafios;
    }
}
