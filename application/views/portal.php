
            <link rel="stylesheet" href="<?php echo base_url("assets/css/portal.css");?>">
            <div class="row">
                <div class="col-md-12 titulo_conteudo">
                    Inscrições para as copas
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-4 col-md-4">
                    <div class="row copa_bronze">
                        <div class="col-xs-4">
                            <img src="<?php echo base_url("assets/images/trofeu.png");?>" class="img_copa">
                        </div>
                        <div class="col-xs-8 descricao_copa">
                            <b>Copa Capitalista</b><br>
                            320 inscritos<br>
                            Começa em:<br>
                            00d 00h 00m 00s<br>
                            <form action="<?php echo base_url("Copa/verifica_copas");?>" method="Post">
                                <input type="hidden" name="copa" value="2">
                                <input type="submit" class="btn btn-success btn_copa" value="Inscreva-se">
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-4 col-md-4">
                    <div class="row copa_prata">
                        <div class="col-xs-4">
                            <img src="<?php echo base_url("assets/images/trofeu.png");?>" class="img_copa">
                        </div>
                        <div class="col-xs-8 descricao_copa">
                            <b>Copa Desafiante</b><br>
                            32 inscritos<br>
                            Começa em:<br>
                            00d 00h 00m 00s<br>
                            <form action="<?php echo base_url("Copa/verifica_copas");?>" method="Post">
                                <input type="hidden" name="copa" value="3">
                                <input type="submit" class="btn btn-success btn_copa" value="Inscreva-se">
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-4 col-md-4">
                    <div class="row copa_ouro">
                        <div class="col-xs-4">
                            <img src="<?php echo base_url("assets/images/trofeu.png");?>" class="img_copa">
                        </div>
                        <div class="col-xs-8 descricao_copa">
                            <b>Copa Lendários</b><br>
                            32 inscritos<br>
                            Começa em:<br>
                            00d 00h 00m 00s<br>
                            <form action="<?php echo base_url("Copa/verifica_copas");?>" method="Post">
                                <input type="hidden" name="copa" value="4">
                                <input type="submit" class="btn btn-success btn_copa" value="Inscreva-se">
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-4 col-md-4">
                    <div class="row copa_liga">
                        <div class="col-xs-4">
                            <img src="<?php echo base_url("assets/images/trofeu.png");?>" class="img_copa">
                        </div>
                        <div class="col-xs-8 liga_descricao_copa">
                            <b>Copa DASDFGFASDFZASD</b><br>
                            32 inscritos<br>
                            Começa em:<br>
                            00d 00h 00m 00s<br>
                            <form action="<?php echo base_url("Copa/verifica_copas");?>" method="Post">
                                <input type="hidden" name="copa" value="1">
                                <input type="hidden" name="liga" value="1">
                                <input type="submit" class="btn btn-success btn_copa" value="Inscreva-se">
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-4 col-md-4">
                    <div class="row copa_liga">
                        <div class="col-xs-4">
                            <img src="<?php echo base_url("assets/images/trofeu.png");?>" class="img_copa">
                        </div>
                        <div class="col-xs-8 liga_descricao_copa">
                            <b>Copa das Ligas</b><br>
                            Para participar<br>
                            Vai na tela das ligas<br>
                            E entre em uma<br>
                            <button class="btn btn-primary btn_copa">Entrar agora</button>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-4 col-md-4">
                    <div class="row copa_liga">
                        <div class="col-xs-4">
                            <img src="<?php echo base_url("assets/images/trofeu.png");?>" class="img_copa">
                        </div>
                        <div class="col-xs-8 liga_descricao_copa">
                            <b>Copa das Ligas</b><br>
                            Para participar<br>
                            Vai na tela das ligas<br>
                            E entre em uma<br>
                            <button class="btn btn-primary btn_copa">Entrar agora</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 titulo_conteudo">
                    Perfil / Estatísticas
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="row perfil">
                        <div class="col-xs-12 col-sm-12 col-md-3">
                            <img src="<?php echo base_url("assets/images/perfil/perfil.jpg");?>" class="img-responsive img_perfil">
                            <p><i>Coloque uma foto em configurações</i></p>
                            <p>Goodnato<p>
                        </div>
                        <div class="col-xs-12 col-sm-6 col-md-3">
                            <p class="titulo_perfil">Estatísticas</p>
                            <p><b>Maior pontuaçao na rodada</b>: 13</p>
                            <p><b>Pontos atuais</b>: 50</p>
                            <p><b>Maior lucro na rodada</b>: M$ 40.00</p>
                            <p><b>Mangos atuais</b>: M$ 104.00</p>
                            <p><b>Vitórias em desafios</b>: 8</p>
                            <p><b>Título nas copas</b>: 3</p>
                            <p><b>Destaques</b>: 5</p>
                        </div>
                        <div class="col-xs-12 col-sm-6 col-md-3">
                            <p class="titulo_perfil">Seus desafios</p>
                            <h3 style="margin: 25px 0;"><a href="#">Desafio individual</a></h3>
                            <h3 style="margin-top: 45px;"><a href="#">Desafio em dupla</a></h3>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-3">
                            <p class="titulo_perfil">Biografia</p>
                            <p>Nenhuma biografia informado</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 titulo_conteudo">
                    Compartilhe com seus amigos
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="row detalhe_rodada">
                        <div class="col-xs-12 col-sm-12 col-md-3">
<?php
    if(!$detalhes_palpites){
?>
                            <p>Aguarde a rodada ser cadastrada<br>
                                <button class="btn btn-success disabled">Aguarde</button>
                            </p>
<?php
    } else if($detalhes_palpites['palpites']){
?>
                            <p>Voce já palpitou nessa rodada :D<br>
                                <a href="<?php echo base_url("Palpites/rodada/$rodada_atual");?>"><button class="btn btn-success">Confira >>></button></a>
                            </p>
<?php
    } else{
?>
                            <p>Voce ainda não palpitou<br>
                                <a href="<?php echo base_url("Palpites/rodada/$rodada_atual");?>"><button class="btn btn-danger">Palpitar >>></button></a>
                            </p>
<?php
    }
?>
                            <p>Mesa Quadrada FC<br>
                            <button class="btn btn-primary">Ir até lá</button></p>
                            <p>SDFASDFGFERSDFG<br>
                            <button class="btn btn-primary">Ir até lá</button></p>
                            <p>SDFASDFGFERSDFG<br>
                            <button class="btn btn-primary">Ir até lá</button></p>
                        </div>
                        <div class="col-xs-12 col-sm-9 col-md-7">
                            <div class="table-responsive">
                                <table class="table table-condensed table-bordered">
<?php
    if($detalhes_palpites){
?>
                                    <tr>
                                        <th>Goodnato | rodada <?php echo $rodada_atual;?></th>
                                        <th>Local</th>
                                        <th>Horário</th>
                                    </tr>
<?php
        $detalhes_rodada= $detalhes_palpites['rodada'];
        if($detalhes_palpites['palpites']){
            $palpites= $detalhes_palpites['palpites'];
            for($i=0; $i<10; $i++){
?>
                                    <tr>
                                        <td><?php echo $detalhes_rodada[$i]["cad_time_mandante"]." ".$palpites[$i]["pap_gol_mandante"]."x".$palpites[$i]["pap_gol_visitante"]." ".$detalhes_rodada[$i]["cad_time_visitante"]." M$".$palpites[$i]["pap_aposta"];?></td>
                                        <td><?php echo $detalhes_rodada[$i]["cad_local"];?></td>
                                        <td><?php $data=new DateTime($detalhes_rodada[$i]["cad_data"]); echo $data->format('d/m H:i')?></td>
                                    </tr>
<?php
            }
        } else{
            foreach ($detalhes_rodada as $value) {
?>
                                    <tr>
                                        <td><?php echo $value["cad_time_mandante"]." "."x"." ".$value["cad_time_visitante"];?></td>
                                        <td><?php echo $value["cad_local"];?></td>
                                        <td><?php $data=new DateTime($value["cad_data"]); echo $data->format('d/m H:i')?></td>
                                    </tr>
<?php
            }
        }
    } else{
        echo "<p><b>Não existe rodada cadastrada no bolão nesse momento. Memorise seus palpites pois em breve iremos cadastrar :)</b></p>";
    }
?>
                                </table>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-3 col-md-2">
                            <h3>Pontos <br> 13</h3>
                            <h3>Lucro <br> M$ 20.00</h3>
                            <h3>CC <br> 2</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 titulo_conteudo">
                    Desafio individual
                </div>
            </div>
            <div class="row desafios">
<?php
    for($i=1; $i<5; $i++){
        if(!isset($desafios[$i])){
?>
                <div class="col-xs-12 col-sm-6 col-md-6 desafio_individual">
                    <form action="<?php echo base_url("Desafios/decisao_desafio");?>" method="Post">
                        <p class="btn_desafio_individual">Desafie um amigo</p>
                        <p><input type="text" class="form-control" name="adversario"></p>
                        <p><button type="submit" class="btn btn-success" name="decisao" value="novo">Desafiar</button> </p>
                    </form>
                </div>
<?php
        } else if(isset ($desafios[$i]) && $desafios[$i]['status'] == 'pendente' && $desafios[$i]['desafiador']){
?>
                <div class="col-xs-12 col-sm-6 col-md-6 desafio_individual">
                    <form action="<?php echo base_url("Desafios/decisao_desafio");?>" method="Post">
                        <input type="hidden" name="adversario" value="<?php echo $desafios[$i]['apelido'];?>">
                        <img src="<?php echo $desafios[$i]['img_perfil'];?>" class="img_desafio">
                        <p><?php echo $desafios[$i]['apelido'];?> te desafiou!<br>
                            <button type="submit" class="btn btn-success" name="decisao" value="aceito">Aceitar</button> 
                            <button type="submit" class="btn btn-danger" name="decisao" value="recusado">Recusar</button>
                        </p>
                    </form>
                </div>
<?php
        } else if(isset ($desafios[$i]) && $desafios[$i]['status'] == 'pendente' && $desafios[$i]['desafiado']){
?>
                <div class="col-xs-12 col-sm-6 col-md-6 desafio_individual">
                    <form action="<?php echo base_url("Desafios/decisao_desafio");?>" method="Post">
                        <input type="hidden" name="adversario" value="<?php echo $desafios[$i]['apelido'];?>">
                        <img src="<?php echo $desafios[$i]['img_perfil'];?>" class="img_desafio">
                        <p>Você desafiou <?php echo $desafios[$i]['apelido'];?><br>
                            <button type="submit" class="btn btn-warning" name="decisao" value="cancelado">Cancelar</button>
                        </p>
                    </form>
                </div>
<?php
        } else if(isset ($desafios[$i]) && $desafios[$i]['status'] == 'aceito'){
?>
                <div class="col-xs-12 col-sm-6 col-md-6 desafio_individual">
                    <div class="row">
                        <div class="col-xs-5 col-md-5">
                            <img src="<?php echo $desafios[0]['img_perfil'];?>" class="img_desafio">
                        </div>
                        <div class="col-xs-2 col-md-2">
                            <p class="vs_desafio">VS</p>
                        </div>
                        <div class="col-xs-5 col-md-5">
                            <img src="<?php echo $desafios[$i]['img_perfil'];?>" class="img_desafio">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-5 col-md-5">
                            <p class="dados_desafio"><?php echo $desafios[0]['apelido'];?></p>
                            <p class="dados_desafio"><?php echo $desafios[0]['pontos']['pontos'];?> pontos</p>
                            <p class="dados_desafio">M$ <?php echo $desafios[0]['pontos']['lucro'];?></p>
                        </div>
                        <div class="col-xs-2 col-md-2">
                            |
                        </div>
                        <div class="col-xs-5 col-md-5">
                            <p class="dados_desafio"><?php echo $desafios[$i]['apelido'];?></p>
                            <p class="dados_desafio"><?php echo $desafios[$i]['pontos']['pontos'];?> pontos</p>
                            <p class="dados_desafio">M$ <?php echo $desafios[$i]['pontos']['lucro'];?></p>
                        </div>
                    </div>
                </div>
<?php
        }
    }
?>
            </div>
            <div class="row">
                <div class="col-md-12 titulo_conteudo">
                    Desafio em dupla
                </div>
            </div>
            <div class="row desafios">
                <div class="col-xs-12 col-sm-6 col-md-6 desafio_dupla">
                    <div class="row">
                        <div class="col-xs-5 col-md-5">
                            <img src="<?php echo base_url("assets/images/perfil/perfil2.jpg");?>" class="img_desafio">
                        </div>
                        <div class="col-xs-2 col-md-2">
                            <p class="vs_desafio">|</p>
                        </div>
                        <div class="col-xs-5 col-md-5">
                            <img src="<?php echo base_url("assets/images/perfil/perfil.jpg");?>" class="img_desafio">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-5 col-md-5">
                            <p class="dados_desafio">DDDDDDJIMA100SANTISTA</p>
                            <p class="dados_desafio">32 pontos</p>
                            <p class="dados_desafio">M$ 100</p>
                        </div>
                        <div class="col-xs-2 col-md-2">
                            |
                        </div>
                        <div class="col-xs-5 col-md-5">
                            <p class="dados_desafio">DDDDDDJIMA100SANTISTA</p>
                            <p class="dados_desafio">32 pontos</p>
                            <p class="dados_desafio">M$ 100</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <p class="placar">32 pontos | M$ 15</p>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6 col-md-6 desafio_dupla">
                    <p class="aguard_desafio_dupla">Aguardando adversário</p>
                </div>
            </div>
        </div>
        <script src="<?php echo base_url("assets/js/portal.js");?>"></script>
    </body>
</html>
