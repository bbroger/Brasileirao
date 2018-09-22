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
    public function total_dados_desafio_user($id){
        $search['sql_desafiador']= "SELECT COUNT(dei_id_user_desafiador) AS desafiador FROM dei_desafios_individual WHERE dei_id_user_desafiador= :id AND dei_status= :status AND YEAR(dei_created)= :year";
        $search['sql_desafiado']= "SELECT COUNT(dei_id_user_desafiado) AS desafiado FROM dei_desafios_individual WHERE dei_id_user_desafiado= :id AND dei_status= :status AND YEAR(dei_created)= :year";
        $search['sql_venceu']= "SELECT COUNT(dei_vencedor) AS venceu FROM dei_desafios_individual WHERE dei_vencedor= :id AND dei_status= :status AND YEAR(dei_created)= :year";
        $search['sql_total_aceitos']= "SELECT COUNT(dei_id_desafio) AS total FROM dei_desafios_individual "
                . "WHERE (dei_id_user_desafiador= :id OR dei_id_user_desafiado= :id) AND dei_status= :status AND YEAR(dei_created)= :year ";
        $search['sql_total_pendentes']= "SELECT COUNT(dei_id_desafio) AS total FROM dei_desafios_individual "
                . "WHERE (dei_id_user_desafiador= :id OR dei_id_user_desafiado= :id) AND dei_status= :status AND YEAR(dei_created)= :year ";
        
        foreach($search AS $key=>$value){
            $stmt= $this->con->prepare($value);
            $stmt->bindValue(':id', $id);
            $stmt->bindValue(':status', ($key == 'sql_total_pendentes') ? 'pendente' : 'aceito');
            $stmt->bindValue(':year', date('Y'));
            $stmt->execute();

            $prov[$key]= $stmt->fetch(PDO::FETCH_ASSOC);
        }
        
        $dados_desafio['desafiador'] = $prov['sql_desafiador']['desafiador'];
        $dados_desafio['desafiado'] = $prov['sql_desafiado']['desafiado'];
        $dados_desafio['venceu'] = $prov['sql_venceu']['venceu'];
        $dados_desafio['total_aceitos'] = $prov['sql_total_aceitos']['total'];
        $dados_desafio['total_pendentes'] = $prov['sql_total_pendentes']['total'];

        $stmt = null;
        return $dados_desafio;
    }
    
    public function novo_desafio_individual($id_desafiador, $apelido){
        $tras_id_desafiado= $this->tras_id_desafiado($apelido);
        if(!$tras_id_desafiado){
            $valida['valida']= false;
            $valida['msg']= "Esse usuário $apelido não foi encontrado :S. Informe corretamente para poder desafia-lo";
            
            return $valida;
        }
        $id_desafiado= $tras_id_desafiado['use_id_user'];
        
        $verifica= $this->verifica_existente_desafio(1, $id_desafiador, $id_desafiado);
        if($verifica){
            $valida['valida']= false;
            $valida['msg']= "Já existe um desafio aceito ou pendente entre você e $apelido ;). É PRA GANHAR HEIN?! X1 É SAGRADO!!";
            
            return $valida;
        }
        
        $valida['valida']= true;
        $valida['msg']= "Você desafiou o $apelido! Representa nesse x1 hein. Outra coisa, foi descontado 1 mango de você. Vença esse desafio e receba 2 de volta.";
        
        return $valida;
    }
    
    private function tras_id_desafiado($apelido){
        $sql= "SELECT use_id_user FROM use_users WHERE use_nickname = ?";
        $stmt= $this->con->prepare($sql);
        $stmt->bindValue(1, $apelido);
        $stmt->execute();
        
        $id= $stmt->fetch(PDO::FETCH_ASSOC);
        
        $stmt= null;
        return $id;
    }
    
    private function verifica_existente_desafio($rodada, $id_desafiador, $id_desafiado){
        $sql= "SELECT dei_id_desafio FROM dei_desafios_individual "
                . "WHERE dei_rodada= ? "
                . "AND ((dei_id_user_desafiador = ? AND dei_id_user_desafiado= ?) "
                . "OR (dei_id_user_desafiador = ? AND dei_id_user_desafiado= ?)) "
                . "AND (dei_status= 'aceito' OR dei_status= 'pendente') "
                . "AND YEAR(dei_created)= ?";
        $stmt= $this->con->prepare($sql);
        $stmt->bindValue(1, $rodada);
        $stmt->bindValue(2, $id_desafiador);
        $stmt->bindValue(3, $id_desafiado);
        $stmt->bindValue(4, $id_desafiado);
        $stmt->bindValue(5, $id_desafiador);
        $stmt->bindValue(6, date('Y'));
        $stmt->execute();
        
        if($stmt->fetch(PDO::FETCH_ASSOC)){
            $stmt= null;
            return true;
        }
        
        $stmt= null;
        return false;
    }

}
