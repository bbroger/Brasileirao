<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Carrega a class Liga_model
 * 
 * Nova liga ou consultar elas
 */
class Liga_model extends CI_Model {

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
     * Verifica se o ID da liga informado existe
     * 
     * @used-by Copa::verifica_copas()            Verifica se a liga que recebeu existe no bolao.
     * @param type $id_liga                       ID da liga
     * @return bool
     */
    public function verifica_liga($id_liga){
        $sql= "SELECT * FROM lig_ligas WHERE lig_id_liga = ? AND lig_status= ?";
        $stmt= $this->con->prepare($sql);
        $stmt->bindValue(1, $id_liga);
        $stmt->bindValue(2, "ativo");
        $stmt->execute();
        
        $valida= $stmt->fetch();
        $stmt= null;
        
        return $valida;
    }

    /**
     * Verifica se o nome da liga informado existe
     * 
     * @used-by Liga::verifica_copas()            Verifica se a liga que recebeu já existe para fazer um novo cadastro.
     * @param type $nome_liga                     Nome da liga
     * @return bool
     */
    public function verifica_nome_liga($nome_liga){
        $sql= "SELECT * FROM lig_ligas WHERE lig_nome = ?";
        $stmt= $this->con->prepare($sql);
        $stmt->bindValue(1, $nome_liga);
        $stmt->execute();
        
        $valida= $stmt->fetch();
        $stmt= null;
        
        return $valida;
    }
    
    /**
     * Verifica se um usuario participa de uma liga
     * 
     * @used-by Copa::verifica_copas()            Verifica se o usuario pertence a essa liga
     * @param type $id_liga                       ID liga
     * @param type $id_usuario                    Usuario
     * @return type
     */
    public function verifica_user_liga($id_liga, $id_usuario){
        $sql= "SELECT mel_id_inscricao FROM mel_membros_ligas WHERE mel_id_liga = ? AND mel_id_user= ? AND mel_status= ?";
        $stmt= $this->con->prepare($sql);
        $stmt->bindValue(1, $id_liga);
        $stmt->bindValue(2, $id_usuario);
        $stmt->bindValue(3, "ativo");
        $stmt->execute();
        
        $valida= $stmt->fetch();
        $stmt= null;
        
        return $valida;
    }
    
    /**
     * Pega todas as ligas do usuário
     * 
     * @used-by Adm_lib::todos_dados_usuarios()         Caso seja requerido as ligas do usuário, puxará todas.
     * @param int $id_usuario
     * @return bool|array
     */
    public function todas_ligas_user($id_usuario){
        $sql= "SELECT liga.* FROM lig_ligas AS liga INNER JOIN mel_membros_ligas AS membro ON liga.lig_id_liga = membro.mel_id_liga "
                . "WHERE liga.lig_status= ? AND membro.mel_status= ? AND membro.mel_id_user= ?";
        $stmt= $this->con->prepare($sql);
        $stmt->bindValue(1, "ativo");
        $stmt->bindValue(2, "ativo");
        $stmt->bindValue(3, $id_usuario);
        $stmt->execute();
        
        $ligas= $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt= null;
        
        if(!$ligas){
            return false;
        }
        
        return $ligas;
    }
}
