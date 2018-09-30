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
     * Pega todas as participaçoes das copas oficiais, total inscritos e todos titulos se houver.
     * 
     * @used-by Adm_lib::todos_dados_usuarios()           Pega o saldo da copa e somará com os mangos.
     * @param int $id
     * @return array|bool
     */
    public function total_copas_por_id($id) {
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
                . "FROM cac_cadastrar_copas WHERE cac_oitavas= :id AND YEAR(cac_created)= :year";
        $stmt = $this->con->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->bindValue(':year', date('Y'));
        $stmt->execute();

        $dados_copa = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt = null;

        if ($dados_copa) {
            foreach ($dados_copa AS $key => $value) {
                $copa[$key]['id'] = $value['id'];
                $copa[$key]['copa'] = $value['copa'];
                $copa[$key]['liga'] = $value['liga'];
                $copa[$key]['rodada'] = $value['rodada'];
                if ($value['liga']) {
                    $copa[$key]['quartas'] = $value['quartas_liga'];
                    $copa[$key]['semi'] = $value['semi_liga'];
                    $copa[$key]['final'] = $value['final_liga'];
                    $copa[$key]['campeao'] = $value['campeao_liga'];
                    $copa[$key]['campeao_copa'] = $value['campeao_copa_liga'];
                    $copa[$key]['inscritos'] = $value['inscritos_liga'];
                    $copa[$key]['saldo'] = ($value['campeao_copa_liga']) ? $value['inscritos_liga'] * 3 - 3 : -3;
                } else {
                    $copa[$key]['quartas'] = $value['quartas'];
                    $copa[$key]['semi'] = $value['semi'];
                    $copa[$key]['final'] = $value['final'];
                    $copa[$key]['campeao'] = $value['campeao'];
                    $copa[$key]['campeao_copa'] = $value['campeao_copa'];
                    $copa[$key]['inscritos'] = $value['inscritos'];
                    $copa[$key]['saldo'] = ($value['campeao']) ? $value['inscritos'] * 5 - 5 : -5;
                }
            }
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
        
        if(!$dados_copa){
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
                $copa[$value['id']][$key]['saldo'] = ($value['campeao_copa_liga']) ? $value['inscritos_liga'] * 3 - 3 : -3;
            } else {
                $copa[$value['id']][$key]['quartas'] = $value['quartas'];
                $copa[$value['id']][$key]['semi'] = $value['semi'];
                $copa[$value['id']][$key]['final'] = $value['final'];
                $copa[$value['id']][$key]['campeao'] = $value['campeao'];
                $copa[$value['id']][$key]['campeao_copa'] = $value['campeao_copa'];
                $copa[$value['id']][$key]['inscritos'] = $value['inscritos'];
                $copa[$value['id']][$key]['saldo'] = ($value['campeao']) ? $value['inscritos'] * 5 - 5 : -5;
            }
        }

        return $copa;
    }

}
