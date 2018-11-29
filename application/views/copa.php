
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
                    <p class="valor_part_copa">10/2</p>
                </div>
                <div class="col-xs-12 col-sm-3 col-md-3 copa_bronze">
                    <p class="titulo_user_copa">Copa Capitalista</p>
                    <img src="<?php echo base_url("assets/images/trofeu.png");?>" class="img_user_copa">
                    <p class="part_copa">Participações/Títulos</p>
                    <p class="valor_part_copa">10/2</p>
                </div>
                <div class="col-xs-12 col-sm-3 col-md-3 copa_prata">
                    <p class="titulo_user_copa">Copa Desafiante</p>
                    <img src="<?php echo base_url("assets/images/trofeu.png");?>" class="img_user_copa">
                    <p class="part_copa">Participações/Títulos</p>
                    <p class="valor_part_copa">10/2</p>
                </div>
                <div class="col-xs-12 col-sm-3 col-md-3 copa_ouro">
                    <p class="titulo_user_copa">Copa Lendários</p>
                    <img src="<?php echo base_url("assets/images/trofeu.png");?>" class="img_user_copa">
                    <p class="part_copa">Participações/Títulos</p>
                    <p class="valor_part_copa">10/2</p>
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
                    <form>
                        <div class="form-group">
                            <label for="exampleInputEmail1">Selecione a rodada</label>
                            <select class="form-control">
                                <option>Rodada 1 - 20/02 14:00</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="exampleInputEmail1">Selecione a copa</label>
                            <select class="form-control">
                                <option>Copa da liga</option>
                            </select>
                        </div>
                        <button class="btn btn-success btn_pesquisa_copa">Pesquisar</button>
                    </form>
                </div>
                <div class="col-xs-12 col-sm-6 col-md-6">
                    <p class="titulo_procura_copa">Últimas copas disputadas</p>
                    <div class="table-responsive">
                        <table class="table table-condensed table-bordered table-striped">
                            <tr>
                                <th>#</th>
                                <th>Data</th>
                                <th>Copa</th>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>12/02 14:00</td>
                                    <td>Copa ASDASDASDASDASD</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>12/02 14:00</td>
                                <td>Copa Lendário</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>12/02 14:00</td>
                                <td>Copa Lendário</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>12/02 14:00</td>
                                <td>Copa Lendário</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>12/02 14:00</td>
                                <td>Copa Lendário</td>
                            </tr>
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
                            <td class="final"><p style="margin: 0; font-weight: bold; font-size: 20px">Grande final</p></td>
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
