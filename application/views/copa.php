
            <link rel="stylesheet" href="<?php echo base_url("assets/css/copa.css");?>">
            <div class="row">
                <div class="col-md-12 titulo_conteudo">
                    Suas copas
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-3 col-md-3 copa_liga">
                    <p class="titulo_user_copa">Copa das ligas</p>
                    <img src="<?php echo base_url("assets/images/trofeu.png");?>" class="img_user_copa">
                    <p class="part_copa">Participações/Títulos</p>
                    <p class="valor_part_copa"><?php if($copas){echo $copas['total_participacao'][1]." / ".$copas['total_titulos'][1];} else{ echo "0 / 0";} ?></p>
                </div>
                <div class="col-xs-12 col-sm-3 col-md-3 copa_bronze">
                    <p class="titulo_user_copa">Copa Capitalista</p>
                    <img src="<?php echo base_url("assets/images/trofeu.png");?>" class="img_user_copa">
                    <p class="part_copa">Participações/Títulos</p>
                    <p class="valor_part_copa"><?php if($copas){echo $copas['total_participacao'][2]." / ".$copas['total_titulos'][2];} else{ echo "0 / 0";} ?></p>
                </div>
                <div class="col-xs-12 col-sm-3 col-md-3 copa_prata">
                    <p class="titulo_user_copa">Copa Desafiante</p>
                    <img src="<?php echo base_url("assets/images/trofeu.png");?>" class="img_user_copa">
                    <p class="part_copa">Participações/Títulos</p>
                    <p class="valor_part_copa"><?php if($copas){echo $copas['total_participacao'][3]." / ".$copas['total_titulos'][3];} else{ echo "0 / 0";} ?></p>
                </div>
                <div class="col-xs-12 col-sm-3 col-md-3 copa_ouro">
                    <p class="titulo_user_copa">Copa Lendários</p>
                    <img src="<?php echo base_url("assets/images/trofeu.png");?>" class="img_user_copa">
                    <p class="part_copa">Participações/Títulos</p>
                    <p class="valor_part_copa"><?php if($copas){echo $copas['total_participacao'][4]." / ".$copas['total_titulos'][4];} else{ echo "0 / 0";} ?></p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 titulo_conteudo">
                    Tabela de eliminatórias
                </div>
            </div>
            <div class="row white" style="padding-bottom: 10px;">
                <div class="col-xs-12 col-sm-6 col-md-6">
                    <p class="titulo_procura_copa">Procure uma copa</p>
                    <div class="form-group">
                        <label for="exampleInputEmail1">Selecione a copa</label>
                        <select class="form-control" id="id_copa">
                            <option value="2">Selecione</option>
<?php
    foreach($id_copa AS $key=>$value){
        if($key == 1){
            if($ligas){
                foreach ($ligas as $chave => $valor) {
?>
                            <option value="<?php echo $valor['lig_id_liga'];?>">Copa <?php echo $valor['lig_nome'];?></option>
<?php
                }
            }
        } else{
                
?>
                            <option value="<?php echo $key;?>"><?php echo $value['nome'];?></option>
<?php
        }
    }
?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1">Selecione a rodada</label>
                        <select class="form-control" id="rodada_id_copa">
                            <option>Selecione primeiro a copa</option>
                        </select>
                    </div>
                    <button class="btn btn-success btn_pesquisa_copa" id="pesquisa_id_copa">Pesquisar</button>
                </div>
                <div class="col-xs-12 col-sm-6 col-md-6">
                    <p class="titulo_procura_copa">Últimas copas disputadas</p>
                    <div class="table-responsive">
                        <table class="table table-condensed table-bordered table-striped">
                            <tr>
                                <th>#</th>
                                <th>Data</th>
                                <th>Copa</th>
                                <th>Status</th>
                            </tr>
<?php
    $total_copas= ( $copas && count($copas)-2 >= 5) ? count($copas)-2 : 5;
    for($i= $total_copas; $i >0; $i--){
        if($copas && array_key_exists($i-1, $copas)){
?>
                            <tr>
                                <td><?php echo $i;?></td>
                                <td><?php echo $rodadas_cadastradas[$copas[$i-1]['rodada']]['inicio_string'];?></td>
                                <td><?php echo $copas[$i-1]['nome_copa'];?></td>
                                <td>
<?php 
    if($copas[$i-1]['campeao'] != null){
        echo "Campeão";
    } else if($copas[$i-1]['final'] != null){
        echo "Final";
    } else if($copas[$i-1]['semi'] != null){
        echo "Semi final";
    } else if($copas[$i-1]['quartas'] != null){
        echo "Quartas";
    } else if($copas[$i-1]['oitavas'] != null){
        echo "Oitavas";
    }
?>
                                </td>
                            </tr>
<?php
    } else{    
?>
                            <tr>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                            </tr>
<?php
    }
}    
?>
                        </table>
                    </div>
                </div>
            </div>
            <div class="row white">
                <div class="table-responsive">
                    <table class="table table-condensed table-bordered">
                        <tr>
                            <th class="oitavas">Oitavas</th>
                            <th class="quartas">Quartas</th>
                            <th class="semi">Semi</th>
                            <th class="final">Final</th>
                            <th class="semi">Semi</th>
                            <th class="quartas">Quartas</th>
                            <th class="oitavas">Oitavas</th>
                        </tr>
                        <tr>
                            <td class="oitavas" id="oitavas_1"></td>
                            <td></td>
                            <td></td>
                            <td class="final"><p class="descricao_copa"><b><span id="nome_copa"></span></b><br>Rodada <span id="rodada_copa"></span> - <span id="data_copa"></span><br><b>Campeão:</b></p></td>
                            <td></td>
                            <td></td>
                            <td class="oitavas" id="oitavas_9"></td>
                        </tr>
                        <tr>
                            <td class="oitavas"></td>
                            <td class="quartas" id="quartas_1"></td>
                            <td></td>
                            <td class="final" id="campeao"></td>
                            <td></td>
                            <td class="quartas" id="quartas_5"></td>
                            <td class="oitavas"></td> 
                        </tr>
                        <tr>
                            <td class="oitavas" id="oitavas_2"></td>
                            <td class="quartas"></td>
                            <td></td>
                            <td class="final"><p class="descricao_copa"><b>Inscritos:</b> <span id="inscritos"></span><br><b>Premiação:</b> M$ <span id="premiacao"></span></p></td>
                            <td></td>
                            <td class="quartas"></td>
                            <td class="oitavas" id="oitavas_10"></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td class="quartas"></td>
                            <td class="semi" id="semi_1"></td>
                            <td></td>
                            <td class="semi" id="semi_3"></td>
                            <td class="quartas"></td>
                            <td></td> 
                        </tr>
                        <tr>
                            <td class="oitavas" id="oitavas_3"></td>
                            <td class="quartas"></td>
                            <td class="semi"></td>
                            <td></td>
                            <td class="semi"></td>
                            <td class="quartas"></td>
                            <td class="oitavas" id="oitavas_11"></td>
                        </tr>
                        <tr>
                            <td class="oitavas"></td>
                            <td class="quartas" id="quartas_2"></td>
                            <td class="semi"></td>
                            <td></td>
                            <td class="semi"></td>
                            <td class="quartas" id="quartas_7"></td>
                            <td class="oitavas"></td> 
                        </tr>
                        <tr>
                            <td class="oitavas" id="oitavas_4"></td>
                            <td></td>
                            <td class="semi"></td>
                            <td class="final" id="final_1"></td>
                            <td class="semi"></td>
                            <td></td>
                            <td class="oitavas" id="oitavas_12"></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td class="semi"></td>
                            <td class="final"><p style="margin: 0; font-weight: bold; font-size: 20px"><a href="#" id="focus_copa"> </a>Grande final</p></td>
                            <td class="semi"></td>
                            <td></td>
                            <td></td> 
                        </tr>
                        <tr>
                            <td class="oitavas" id="oitavas_5"></td>
                            <td></td>
                            <td class="semi"></td>
                            <td class="final" id="final_2"></td>
                            <td class="semi"></td>
                            <td></td>
                            <td class="oitavas" id="oitavas_13"></td>
                        </tr>
                        <tr>
                            <td class="oitavas"></td>
                            <td class="quartas" id="quartas_3"></td>
                            <td class="semi"></td>
                            <td></td>
                            <td class="semi"></td>
                            <td class="quartas" id="quartas_7"></td>
                            <td class="oitavas"></td> 
                        </tr>
                        <tr>
                            <td class="oitavas" id="oitavas_6"></td>
                            <td class="quartas"></td>
                            <td class="semi"></td>
                            <td></td>
                            <td class="semi"></td>
                            <td class="quartas"></td>
                            <td class="oitavas" id="oitavas_14"></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td class="quartas"></td>
                            <td class="semi" id="semi_2"></td>
                            <td></td>
                            <td class="semi" id="semi_4"></td>
                            <td class="quartas"></td>
                            <td></td> 
                        </tr>
                        <tr>
                            <td class="oitavas" id="oitavas_7"></td>
                            <td class="quartas"></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="quartas"></td>
                            <td class="oitavas" id="oitavas_15"></td>
                        </tr>
                        <tr>
                            <td class="oitavas"></td>
                            <td class="quartas" id="quartas_4"></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="quartas" id="quartas_8"></td>
                            <td class="oitavas"></td> 
                        </tr>
                        <tr>
                            <td class="oitavas" id="oitavas_8"></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="oitavas" id="oitavas_16"></td>
                        </tr>
                        <tr>
                            <th class="oitavas">Oitavas</th>
                            <th class="quartas">Quartas</th>
                            <th class="semi">Semi</th>
                            <th class="final">Final</th>
                            <th class="semi">Semi</th>
                            <th class="quartas">Quartas</th>
                            <th class="oitavas">Oitavas</th>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <script src="<?php echo base_url("assets/js/copa.js");?>"></script>
        <script>
            consultar_copa(<?php echo $rodada.", ".$copa.", ".$liga;?>);
        </script>
    </body>
</html>
