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
        $search['sql_total_aceitos']= "SELECT COUNT(dei_id_desafio) AS total FROM dei_desafios_individual WHERE (dei_id_user_desafiador= :id OR dei_id_user_desafiado= :id) AND dei_status= :status AND YEAR(dei_created)= :year ";
        $search['sql_total_pendentes']= "SELECT COUNT(dei_id_desafio) AS total FROM dei_desafios_individual WHERE (dei_id_user_desafiador= :id OR dei_id_user_desafiado= :id) AND dei_status= :status AND YEAR(dei_created)= :year ";
        
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

}
