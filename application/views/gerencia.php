
            <link rel="stylesheet" href="<?php echo base_url("assets/css/gerencia.css");?>">
            <div class="row">
                <div class="col-md-12 titulo_conteudo">
                    Cadastrar rodadas
                </div>
            </div>
            <div class="row white">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-xs-12 col-sm-4 col-sm-offset-4 col-lg-2 col-lg-offset-5 procura_palpite">
                            <form>
                                <div class="form-group">
                                    <select class="form-control" id="rodadas_cadastradas">
<?php
    for($i= 1; $i <= 38; $i++){
        $ii= ($i < 10) ? "0".$i : $i;
        if(array_key_exists($i, $rodadas_cadastradas)){
            echo "<option value='$i'>Rodada $ii - ". $rodadas_cadastradas[$i]["inicio_string"] ."</option>";
        } else{
            echo "<option value='$i'>Rodada $ii - Cadastrar</option>";
        }
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
            <div class="row" style="background-color: white;">
                <p class="text-center"><b id="info_rodada"></b></p>
                <div class="table-responsive">
                    <form id="gerenciar_rodada" action="" method="Post">
                        <table class="table table-bordered table-bordered">
                            <input type="hidden" name="manipular" id="manipular" value="">
                            <input type="hidden" name="rodada_solic" id="rodada_solic" value="">
                            <tr>
                                <th>#</th>
                                <th>Time mandante</th>
                                <th>Rodada 1</th>
                                <th>Time visitante</th>
                                <th>Local</th>
                                <th>Data</th>
                                <th>Resultado</th>
                            </tr>
<?php
    for($i= 1; $i <= 10; $i++){
?>
                            <tr>
                                <td><p style="margin: 7px 0 0 0;"><?php echo $i;?></p></td>
                                <td>
                                    <select class="form-control input_times_gerencia" name="time_mandante_<?php echo $i;?>" id="time_mandante_<?php echo $i;?>">
                                        <option value="0">Selecione</option>
<?php
    foreach ($times as $key => $value) {
?>
                                        <option value="<?php echo $key;?>" <?php echo set_select('time_mandante_'.$i, $key); ?>><?php echo $value;?></option>
<?php
    }
?>
                                    </select>
                                </td>
                                <td>
                                    <p class="input_gol">
                                        <input type="text" class="gol_gerencia" name="gol_mandante_<?php echo $i;?>" id="gol_mandante_<?php echo $i;?>"> 
                                        x 
                                        <input type="text" class="gol_gerencia" name="gol_visitante_<?php echo $i;?>" id="gol_visitante_<?php echo $i;?>">
                                    </p>
                                </td>
                                <td>
                                    <select class="form-control input_times_gerencia" name="time_visitante_<?php echo $i;?>" id="time_visitante_<?php echo $i;?>">
                                        <option value="0">Selecione</option>
<?php
    foreach ($times as $key => $value) {
?>
                                        <option value="<?php echo $key;?>" <?php echo set_select('time_visitante_'.$i, $key); ?>><?php echo $value;?></option>
<?php
    }
?>
                                    </select>
                                </td>
                                <td><input type="text" class="form-control input_local_gerencia" name="local_partida_<?php echo $i;?>" id="local_partida_<?php echo $i;?>" value="<?php echo set_value('local_partida_'.$i); ?>" placeholder="OPCIONAL"></td>
                                <td><input type="datetime-local" class="form-control input_date_gerencia" name="data_partida_<?php echo $i;?>" id="data_partida_<?php echo $i;?>" value="<?php echo set_value('data_partida_'.$i); ?>" placeholder="DD/MM/YYYY HH:mm"></td>
                                <td><button type="button" class="btn btn_enviar_resul" name="btn_partida_<?php echo $i;?>" id="btn_partida_<?php echo $i;?>" value="<?php echo $i;?>">Enviar</button></td>
                            </tr>
<?php
    }
?>
                            <tr>
                                <td><p style="margin: 7px 0 0 0;">#</p></td>
                                <td><input type="submit" class="btn" id="btn_gerenciar_rodada" value=""></td>
                                <td colspan="5"></td>
                            </tr>
                        </table>
                    </form>
                </div>
            </div>
        </div>
        <script src="<?php echo base_url("assets/js/gerencia.js");?>"></script>
        <script>
            consultar_rodada(<?php echo $rodada.", ".$form;?>);
        </script>
    </body>
</html>
