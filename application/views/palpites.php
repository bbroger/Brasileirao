
            <link rel="stylesheet" href="<?php echo base_url("assets/css/palpites.css");?>">
            <div class="row">
                <div class="col-md-12 titulo_conteudo">
                    Dados para os palpites
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-4 dados_palpites">
                    <p class="dados_palpites_titulo">Último lucro</p>
                    <p class="dados_palpites_valor">M$ -15</p>
                </div>
                <div class="col-xs-12 col-sm-4 dados_palpites">
                    <p class="dados_palpites_titulo">Última pontuação</p>
                    <p class="dados_palpites_valor">32 pontos</p>
                </div>
                <div class="col-xs-12 col-sm-4 dados_palpites">
                    <p class="dados_palpites_titulo">Mangos atual</p>
                    <p class="dados_palpites_valor">M$ 100</p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 titulo_conteudo">
                    Palpites
                </div>
            </div>
            <div class="row white">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-xs-12 col-sm-4 col-sm-offset-4 col-lg-2 col-lg-offset-5 procura_palpite">
                            <form>
                                <div class="form-group">
                                    <label for="exampleInputPassword1">Procure uma rodada</label>
                                    <select class="form-control" id="rodadas_cadastradas">
<?php
    if($rodadas_cadastradas){
        foreach ($rodadas_cadastradas as $key => $value) {
            $ii= ($key < 10) ? "0".$key : $key;
            echo "<option value='$key'>Rodada $ii | ".$value["inicio_string"]."</option>";
        }
    } else{
        echo "<option>Nenhuma rodada</option>";
    }
?>
                                    </select>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <button style="float: left;" class="btn btn-link" id="anterior_rodada" value=""><<< Anterior</button>
                    <button style="float: right;" class="btn btn-link" id="proximo_rodada" value="">Próximo >>></button>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 white">
                    <p class="text-center"><b id="info_rodada"></b></p>
                    <div class="table-responsive">
                        <form id="form_palpites" action="<?php echo base_url("Palpites/enviar_palpites/$rodada");?>" method="Post" autocomplete="off">
                            <table class="table table-bordered table_palpites">
                                <thead>
                                    <tr>
                                        <th><a href="#" id="focus_palpite">#</a></th>
                                        <th colspan="3">Rodada <?php echo $rodada;?></th>
                                        <th>Aposta</th>
                                        <th>Pontos</th>
                                        <th>Lucro</th>
                                        <th>Data</th>
                                        <th>Local</th>
                                    </tr>
                                </thead>
                                <tbody id="clear_table">
<?php
    if(array_key_exists($rodada, $rodadas_cadastradas)){
        for($i= 1; $i <= 10; $i++){
?>
                                    <tr>
                                        <td><p class="margin_palpite"><?php echo $i;?></p></td>
                                        <td id="img_mandante_<?php echo $i;?>"></td>
                                        <td>
                                            <p class="margin_palpite_input">
                                                <input value="<?php echo set_value("palpite_mandante_$i");?>" type="text" class="input_palpite clear_input" name="palpite_mandante_<?php echo $i;?>" id="palpite_mandante_<?php echo $i;?>"> 
                                                x
                                                <input value="<?php echo set_value("palpite_visitante_$i");?>" type="text" class="input_palpite clear_input" name="palpite_visitante_<?php echo $i;?>" id="palpite_visitante_<?php echo $i;?>">
                                            </p>
                                            <p class="gols_times">
                                                <span id="gol_mandante_<?php echo $i;?>"></span>
                                                &nbsp;&nbsp; x &nbsp;&nbsp;
                                                <span id="gol_visitante_<?php echo $i;?>"></span>
                                            </p>
                                        </td>
                                        <td id="img_visitante_<?php echo $i;?>"></td>
                                        <td><p class="margin_palpite">M$ <input value="<?php echo set_value("aposta_partida_$i");?>" type="text" class="input_palpite clear_input" name="aposta_partida_<?php echo $i;?>" id="aposta_partida_<?php echo $i;?>"></p></td>
                                        <td><p class="margin_palpite" id="pontos_partida_<?php echo $i;?>"></p></td>
                                        <td><p class="margin_palpite" id="lucro_partida_<?php echo $i;?>"></p></td>
                                        <td><p class="margin_palpite" id="data_partida_<?php echo $i;?>"></p></td>
                                        <td><p class="margin_palpite" id="local_partida_<?php echo $i;?>"></p></td>
                                    </tr>
<?php
        }
    }
?>
                                    <tr>
                                        <td colspan="2"><input id="btn_palpitar" type="submit" class="btn btn-success" value=""></td>
                                        <td></td>
                                        <td></td>
                                        <td><b id="total_mangos_palpites"></b></td>
                                        <td><b id="total_pontos_palpites"></b></td>
                                        <td><b id="total_lucro_palpites"></b></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <script src="<?php echo base_url("assets/js/palpites.js");?>"></script>
        <script>
            consultar_rodada(<?php echo $rodada.", ".$form;?>);
        </script>
    </body>
</html>
