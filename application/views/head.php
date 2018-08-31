<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta charset="UTF-8">
        <title>Brasileirao</title>
        <link rel="stylesheet" href="<?php echo base_url("assets/css/bootstrap.css");?>">
        <link rel="stylesheet" href="<?php echo base_url("assets/css/brasileirao.css");?>">
        <script src="<?php echo base_url("assets/js/jquery.js");?>"></script>
        <script src="<?php echo base_url("assets/js/bootstrap.js");?>"></script>
        <script src="<?php echo base_url("assets/js/moments.js");?>"></script>
        <script src="<?php echo base_url("assets/js/brasileirao.js");?>"></script>
        <script>
            function ajax(onde) {
                var url = "<?php echo base_url(); ?>";
                return url + onde;
            }
        </script>
    </head>
    <body>
        <nav class="navbar navbar-default navbar-fixed-top">
            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="<?php echo base_url("Portal");?>">Chute Certo</a>
                </div>
                <div id="navbar" class="navbar-collapse collapse">
                    <ul class="nav navbar-nav">
                        <li><a href="<?php echo base_url("Palpites");?>">Palpites</a></li>
                        <li><a href="<?php echo base_url("Copa");?>">Copas</a></li>
                        <li><a href="<?php echo base_url("Liga");?>">Ligas</a></li>
                        <li><a href="<?php echo base_url("Classificacao");?>">Classificação</a></li>
                    </ul>
                    <ul class="nav navbar-nav navbar-right">
                        <li><a href="<?php echo base_url("Acesso_liga");?>">Ajuda</a></li>
                        <li><a href="<?php echo base_url("Gerencia");?>">Configurações</a></li>
                        <li><a href="#">Sair</a></li>
                    </ul>
                </div>
            </div>
        </nav>
        <div class="container-fluid bug">
             <div class="row">
                 <a href="#" id="focus_msg"></a>
                 <div class="alert alert-success" id="msg" style="margin: 0; padding: 0; text-align: center;">
                     <?php echo $msg;?>
                </div>
            </div>
            <div class="row" style="margin-top: 10px;">
                <div class="col-xs-12 col-sm-7 col-md-8 mensagem_gerencia">
                    <p><u>Mensagem da gerencia:</u></p>
                    <p>Bem vindos ao Bfsd ff afads f afas dfasd dsf sdfsdf  sf sdolao</p>
                </div>
                <div class="col-xs-12 col-sm-5 col-md-4 mensagem_rodada_termina">
                    <p>A rodada 1 <b>COMEÇA</b>: Seg 30/02 14:30</p>
                    <p>Resta(m): 00d 00h 00m 00s</p>
                    <p><button class="btn btn-danger">Palpitar</button></p>
                </div>
            </div>