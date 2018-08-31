
            <link rel="stylesheet" href="<?php echo base_url("assets/css/liga.css");?>">
            <div class="row">
                <div class="col-md-12 titulo_conteudo">
                    Suas ligas
                </div>
            </div>
            <div class="row" style="background-color: white;">
                <div class="col-xs-12 col-sm-4 col_user_liga">
                    <img src="<?php echo base_url("assets/images/corinthians.png");?>" class="img_liga">
                    <p class="nome_liga">ASDFASDFASDFASDFASDF</p>
                    <button class="btn btn-primary">Acessar</button> <button class="btn btn-danger">Sair</button>
                </div>
                <div class="col-xs-12 col-sm-4 col_user_liga">
                    <img src="<?php echo base_url("assets/images/corinthians.png");?>" class="img_liga">
                    <p class="nome_liga">ASDFASDFASDFASDFASDF</p>
                    <button class="btn btn-primary">Acessar</button> <button class="btn btn-danger">Sair</button>
                </div>
                <div class="col-xs-12 col-sm-4 col_user_liga">
                    <p class="entre_liga">Entre em uma liga</p>
                    <button class="btn btn-primary">Encontrar liga</button>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 titulo_conteudo">
                    Encontrar ligas
                </div>
            </div>
            <div class="row white">
                <div class="col-md-12">
                    <form>
                        <div class="form-group" style="width: 280px; margin: 10px auto;">
                            <label for="exampleInputEmail1">Encontre uma liga</label>
                            <input type="email" class="form-control" id="exampleInputEmail1" placeholder="Digite o nome da liga">
                        </div>
                        <button type="submit" class="btn btn-default" style="width: 100px; display: block; margin: 10px auto;">Pesquisar</button>
                    </form>
                </div>
            </div>
            <div class="row white">
                <div class="table-responsive">
                    <table class="table table-bordered table-condensed table-striped">
                        <tr>
                            <th>Emblema</th>
                            <th>Nome</th>
                            <th>Descrição</th>
                            <th>Membros</th>
                            <th>Ação</th>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 titulo_conteudo">
                    Ligas recomendadas
                </div>
            </div>
            <div class="row white">
                <div class="table-responsive">
                    <table class="table table-bordered table-condensed table-striped">
                        <tr>
                            <th>Emblema</th>
                            <th>Nome</th>
                            <th>Descrição</th>
                            <th>Membros</th>
                            <th>Ação</th>
                        </tr>
                        <tr>
                            <td><img src="<?php echo base_url("assets/images/corinthians.png");?>" class="img_liga"></td>
                            <td><p class="nome_liga nome_liga_pesquisa">ASDFASDFASDFASDFASDF</p></td>
                            <td><p>Entre na nossa liga para jogar</p></td>
                            <td><p class="nome_liga_pesquisa">32</p></td>
                            <td><p class="nome_liga_pesquisa"><button class="btn btn-primary">Entrar</button></p></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <script src="<?php echo base_url("assets/js/liga.js");?>"></script>
    </body>
</html>
