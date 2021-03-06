<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Carrega a class Palpite_model
 * 
 * Depois que passou na Classe Palpites para aplicar a regra de negócio, aqui irá manipular no banco de dados.
 */
class Palpites_model extends CI_Model {

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
     * Irá trazer os palpites do usuário da rodada solicitada e do ano atual.
     * 
     * @used-by Portal::detalhes_palpites()     Pega os palpites para mostrar compactado no Portal
     * @used-by Palpites::palpites_usuario()    Levá para a view os palpites do usuario da rodada solicitada
     * @used-by Palpites::enviar_palpites()     Ao enviar as partidas, consulta se ja foi palpitado antes e assim irá pegar as partidas que ja iniciou
     * @used-by Palpites::aposta_check()        Irá somar todas as apostas e somar com o total de mangos, como se tivesse devolvendo as apostas para receber novas.        
     * @param int $user_id                      Irá selecionar os palpites do usuário informado
     * @param int $rodada                       Irá selecionar os palpites da rodada informado
     * @return array|bool
     */
    public function palpites_usuario($user_id, $rodada) {
        $sql = "SELECT * FROM pap_palpites "
                . "WHERE pap_user_id= ? AND pap_rodada= ? AND pap_valida= 'sim' AND YEAR(pap_created) = ? "
                . "ORDER BY pap_partida ASC LIMIT 10";
        $stmt = $this->con->prepare($sql);
        $stmt->bindValue(1, $user_id);
        $stmt->bindValue(2, $rodada);
        $stmt->bindValue(3, date('Y'));
        $stmt->execute();

        $tras_palpites = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $stmt= null;
        return $tras_palpites;
    }
    
    /**
     * Irá trazer todos os palpites da rodada, partida e do ano atual para fazer os calculos dos palpites.
     * 
     * @used-by Gerencia::calcula_palpites()        Tras os palpites para fazer o calculo
     * @param int $rodada                           Para trazer a rodada solicitada
     * @param int $partida                          PAra trazere a partida solicitada
     * @return array|bool
     */
    public function todos_palpites_partidas($rodada, $partida) {
        $sql = "SELECT pap_id_palpite, pap_gol_mandante, pap_gol_visitante, pap_aposta FROM pap_palpites "
                . "WHERE pap_rodada= ? AND pap_partida= ? AND pap_palpitou = 'sim' AND pap_valida= 'sim' AND YEAR(pap_created) = ?";

        $stmt = $this->con->prepare($sql);
        $stmt->bindValue(1, $rodada);
        $stmt->bindValue(2, $partida);
        $stmt->bindValue(3, date('Y'));
        $stmt->execute();

        $tras_palpites = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt= null;
        return $tras_palpites;
    }
    
    /**
     * Irá salvar os palpites dos usuarios. Mesmo se o usuario for atualizar ja existente, irá salvar os novos palpites e desconsiderar os antigos.
     * Obs: Caso o usuario esteja editando os palpites, o pap_valida anterior ficará nao e os novos palpites das 10 partidas ficará sim.
     * 
     * @used-by Palpites::enviar_palpites()                         Depois que validou tudo, salva os palpites.  
     * @uses Palpites_model::invalidar_palpites_anttigos()          Sempre manda invalidar mesmo que nao exista para salvar novos palpites.  
     * @param int   $user_id                                        O ID do usuario para salvar na tabela pap_palpites
     * @param int   $rodada                                         Rodada para gravar a rodada que está palpitando
     * @param array $palpites                                       Esse array irá conter todos os palpites da rodada
     * @return void
     */
    public function salvar_palpites($user_id, $rodada, $palpites) {
        $this->invalidar_palpites_antigos($user_id, $rodada);
        $sql = "INSERT INTO pap_palpites (pap_user_id, pap_rodada, pap_partida, pap_gol_mandante, pap_gol_visitante, pap_aposta, pap_lucro, pap_saldo, pap_palpitou) "
                . "VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->con->prepare($sql);
        foreach ($palpites as $key => $value) {
            $stmt->bindValue(1, $user_id);
            $stmt->bindValue(2, $rodada);
            $stmt->bindValue(3, $key);
            $stmt->bindValue(4, $value["gol_mandante"]);
            $stmt->bindValue(5, $value["gol_visitante"]);
            $stmt->bindValue(6, $value["aposta"]);
            $stmt->bindValue(7, $value["lucro"]);
            $stmt->bindValue(8, $value["saldo"]);
            $stmt->bindValue(9, $value["palpitou"]);
            $stmt->execute();
        }
        $stmt= null;
    }
    
    /**
     * Quando um usuário edita os palpites, essa funçao serve para invalidar o palpite antigo para que o novo palpite seja válido.
     * 
     * @used-by Palpites_model::salvar_palpites()           Salva os novos ou os palpites editados como válidos.
     * @param type $user_id
     * @param type $rodada
     * @return void
     */
    private function invalidar_palpites_antigos($user_id, $rodada){
        $sql= "UPDATE pap_palpites SET pap_valida = 'nao' WHERE pap_user_id = ? AND pap_rodada= ? AND YEAR(pap_created) = ?";
        $stmt= $this->con->prepare($sql);
        $stmt->bindValue(1, $user_id);
        $stmt->bindValue(2, $rodada);
        $stmt->bindValue(3, date('Y'));
        $stmt->execute();
        $stmt= null;
    }

}
