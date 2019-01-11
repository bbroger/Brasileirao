
            <link rel="stylesheet" href="<?php echo base_url("assets/css/liga.css");?>">
            <div class="row">
                <div class="col-md-12 titulo_conteudo">
                    Suas ligas
                </div>
            </div>
            <div class="row" style="background-color: white;">
<?php
    for($i=0; $i<3; $i++){
        if($ligas){
            if(array_key_exists($i, $ligas)){
?>
                <div class="col-xs-12 col-sm-4 col_user_liga">
                    <img src="<?php echo base_url("assets/images/".$ligas[$i]['lig_img']);?>" class="img_liga">
                    <p class="nome_liga"><?php echo $ligas[$i]['lig_nome'];?></p>
                    <button class="btn btn-primary">Acessar</button> <button class="btn btn-danger">Sair</button>
                </div>
<?php
            } else{
?>
                <div class="col-xs-12 col-sm-4 col_user_liga">
                    <p class="entre_liga">Entre em uma liga</p>
                    <button class="btn btn-primary">Encontrar liga</button>
                </div>
<?php
            }
        } else {
?>
                <div class="col-xs-12 col-sm-4 col_user_liga">
                    <p class="entre_liga">Entre em uma liga</p>
                    <button class="btn btn-primary">Encontrar liga</button>
                </div>
<?php
        }
    }
?>
            </div>
            <div class="row">
                <div class="col-md-12 titulo_conteudo">
                    Encontrar ligas
                </div>
            </div>
            <div class="row white">
                <div class="col-md-12">
                </div>
            </div>
            <div class="row white">
                <div class="col-md-5 col-md-offset-1">
                    <form>
                        <div class="form-group" style="width: 280px; margin: 10px auto;">
                            <label for="exampleInputEmail1">Encontre uma liga</label>
                            <input type="email" class="form-control" id="exampleInputEmail1" placeholder="Digite o nome da liga">
                        </div>
                        <button type="submit" class="btn btn-default" style="width: 100px; display: block; margin: 10px auto;">Pesquisar</button>
                    </form><br>
                    <div class="table-responsive">
                        <table class="table table-bordered table-condensed table-striped">
                            <tr>
                                <td><img src="<?php echo base_url("assets/images/default.png");?>" class="img_liga"><p class="nome_liga">ASDFASDFASDFASDFASDF</p></td>
                                <td>Aqui voce pode jogar conosco na diversão!</td>
                                <td><p class="nome_liga_pesquisa">32 membros<br><button class="btn btn-primary">Entrar</button></p></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="col-md-1"></div>
                <div class="col-md-4">
                    <p style="text-align: center; color: blue; font-weight: bold;">Crie uma liga agora mesmo!</p>
<?php 
    if (validation_errors()) { 
?>
                    <div class="alert alert-danger alert-dismissable fade in">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
<?php 
    echo validation_errors(); 
?>
                    </div>
<?php 
    } 
?>
                    <form action="<?php echo base_url("Liga/cadastrar_ligas");?>" method="Post">
                        <div class="form-group">
                            <label for="nome" id="label-nome">Nome</label>
                            <input type="text" class="form-control" id="nome" name="nome" placeholder="Até 15 caracteres">
                        </div>
                        <div class="form-group">
                            <label for="descricao" id="label-descricao">Descrição</label>
                            <textarea class="form-control" id="descricao" name="descricao"></textarea>
                            <p class="help-block">15 caracteres</p>
                        </div>
                        <div class="form-group">
                            <label id="label-img" for="img">Imagem da liga</label>
                            <input type="file" id="img" name="img">
                            <p class="help-block" id="msg-img">Formatos aceitos .png .jpeg ou .jpg. Tamanho máximo 2Mb</p>
                        </div>
                        <div class="radio">
                            <label>
                                <input type="radio" name="opcaoliga" id="opcaoliga1" value="ligaberto" checked>
                                Liga aberta para todos entrarem
                            </label>
                        </div>
                        <div class="radio">
                            <label>
                                <input type="radio" name="opcaoliga" id="opcaoliga2" value="ligafechado">
                                Liga fechado sendo necessário mandar convite para entrar
                            </label>
                        </div>
                        <button type="submit" class="btn btn-default">Submit</button>
                    </form>
                </div>
            </div>
        </div>
        <script src="<?php echo base_url("assets/js/liga.js");?>"></script>
    </body>
</html>
