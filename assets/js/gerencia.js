$("#rodadas_cadastradas").change(function () {
    $("#msg").html("");
    var rodada = $(this).val();
    var confere = confere_form_gerencia();
    if (confere) {
        var passa = confirm("As alteraçoes não serão salvas. Quer sair dessa rodada?");
        if (passa) {
            consultar_rodada(rodada);
        }
    } else {
        consultar_rodada(rodada);
    }
});

$("#anterior_rodada, #proximo_rodada").click(function () {
    $("#msg").html("");
    var rodada = $(this).val();
    var confere = confere_form_gerencia();
    if (confere) {
        var passa = confirm("As alteraçoes não serão salvas. Quer sair dessa rodada?");
        if (passa) {
            consultar_rodada(rodada);
        }
    } else {
        consultar_rodada(rodada);
    }
});

function consultar_rodada(rodada, form = 0) {
    if (!$.isNumeric(rodada) || Math.floor(rodada) != rodada) {
        rodada = 1;
    }
    success_color(".input_times_gerencia, .input_date_gerencia, .gol_gerencia");
    $.ajax({
        url: url_js("Gerencia/consultar_rodada/" + rodada),
        type: "Post",
        dataType: "json"
    }).done(function (data) {
        if (data.existe_rodada === 1) {
            $("#info_rodada").html("Rodada " + rodada + " | Inicio: " + moment(data.inicio).format("ddd DD/MM HH:mm") + " | Fim: " + moment(data.fim).format("ddd DD/MM HH:mm"));
            $("#manipular").val("atualizar");
            $(".gol_gerencia, .btn_enviar_resul").prop("disabled", false);

            if (form === 0) {
                $.each(data.rodada_completa, function (i, item) {
                    $("#time_mandante_" + item["cad_partida"]).val(item["cad_time_mandante_var"]);
                    $("#gol_mandante_" + item["cad_partida"]).val(item["cad_time_mandante_gol"]);
                    $("#gol_visitante_" + item["cad_partida"]).val(item["cad_time_visitante_gol"]);
                    $("#time_visitante_" + item["cad_partida"]).val(item["cad_time_visitante_var"]);
                    $("#local_partida_" + item["cad_partida"]).val(item["cad_local"]);
                    $("#data_partida_" + item["cad_partida"]).val(item["cad_data"].replace(" ", "T").slice(0, -3));
                });
            }

            $("#btn_gerenciar_rodada").val("Atualizar");
            $("#rodada_solic").val(rodada);
        } else {
            $("#info_rodada").html("Rodada " + rodada + " não cadastrada");
            $("#manipular").val("cadastrar");

            if (form === 0) {
                for (var i = 1; i <= 10; i++) {
                    $("#time_mandante_" + i).val(0);
                    $("#gol_mandante_" + i).val("");
                    $("#gol_visitante_" + i).val("");
                    $("#time_visitante_" + i).val(0);
                    $("#local_partida_" + i).val("");
                    $("#data_partida_" + i).val("");
                }
            }

            $(".gol_gerencia, .btn_enviar_resul").prop("disabled", true);
            $("#btn_gerenciar_rodada").val("Cadastrar");
        }

        $("#gerenciar_rodada").prop("action", url_js("Gerencia/manipular_detalhes_rodada/" + rodada));
        $("#rodadas_cadastradas").val(rodada);
        var anterior = (rodada > 1 && rodada <= 38) ? rodada - 1 : rodada;
        var proximo = (rodada > 0 && rodada <= 37) ? parseInt(rodada) + 1 : rodada;
        $("#anterior_rodada").val(anterior);
        $("#proximo_rodada").val(proximo);
    }).fail(function (data) {
        $("#focus_msg").focus();
        $("#msg").html("Erro ao trazer as partidas. Tente mais tarde.");
    });
}

$("#btn_partida_1, #btn_partida_2, #btn_partida_3, #btn_partida_4, #btn_partida_5, #btn_partida_6, #btn_partida_7, #btn_partida_8, #btn_partida_9, #btn_partida_10").click(function () {
    var rodada = $("#rodada_solic").val();
    var partida = $(this).val();
    var data_partida = moment($("#data_partida_" + partida).val());
    var gol_mandante = $("#gol_mandante_" + partida).val();
    var gol_visitante = $("#gol_visitante_" + partida).val();

    if (!$.isNumeric(rodada) || Math.floor(rodada) != rodada || rodada < 1 || rodada > 38) {
        $("#focus_msg").focus();
        $("#msg").html("Erro ao enviar o resultado. Essa rodada é inválida.");
    } else if (!$.isNumeric(partida) || Math.floor(partida) != partida || partida < 1 || partida > 10) {
        $("#focus_msg").focus();
        $("#msg").html("Erro ao enviar o resultado. Essa partida é inválida.");
    } else if (!data_partida.isValid() || moment().diff(data_partida, "seconds") < 0) {
        $("#focus_msg").focus();
        $("#msg").html("Partida " + partida + " começa " + data_partida.format("ddd DD/MM HH:mm") + ", aguarde para enviar o resultado..");
    } else if (!$.isNumeric(gol_mandante) || Math.floor(gol_mandante) != gol_mandante || !$.isNumeric(gol_visitante) || Math.floor(gol_visitante) != gol_visitante) {
        $("#focus_msg").focus();
        $("#msg").html("Erro ao enviar o resultado. Insira apenas numeros no gol mandante e visitante");
        error_color("#gol_mandante_" + partida);
        error_color("#gol_visitante_" + partida);
    } else {
        $.ajax({
            url: url_js("Gerencia/enviar_resultado/" + rodada),
            type: "Post",
            data: {gol_mandante: gol_mandante, gol_visitante: gol_visitante, partida: partida},
            dataType: "json"
        }).done(function (data) {
            $("#focus_msg").focus();
            $("#msg").html(data.msg);
            success_color("#gol_mandante_" + partida);
            success_color("#gol_visitante_" + partida);
        }).fail(function (data) {
            $("#focus_msg").focus();
            $("#msg").html("Erro ao enviar o resultado. Tente mais tarde.");
        });
    }
});

$("#gerenciar_rodada").submit(function (e) {
    e.preventDefault();
    success_color(".input_times_gerencia, .input_date_gerencia, .gol_gerencia");
    var valid = true;

    for (var i = 1; i <= 10; i++) {
        if ($("#time_mandante_" + i).val() == 0) {
            error_color("#time_mandante_" + i);
            valid = false;
        }

        if ($("#time_visitante_" + i).val() == 0) {
            error_color("#time_visitante_" + i);
            valid = false;
        }

        var data = moment($("#data_partida_" + i).val(), ["DD/MM/YYYY HH:mm", "YYYY-MM-DDTHH:mm"], true);
        if (!data.isValid()) {
            error_color("#data_partida_" + i);
            valid = false;
        } else if (data.format("YYYY") != moment().format("YYYY")) {
            error_color("#data_partida_" + i);
            valid = false;
        } else if(data.diff(moment(), "seconds") < 0 && $("#btn_gerenciar_rodada").val() == "Cadastrar") {
            error_color("#data_partida_" + i);
            valid = false;
        }

        for (var ii = 1; ii <= 10; ii++) {
            if ($("#time_mandante_" + i).val() === $("#time_mandante_" + ii).val() && i !== ii) {
                error_color("#time_mandante_" + i);
                error_color("#time_mandante_" + ii);
                valid = false;
            }

            if ($("#time_visitante_" + i).val() === $("#time_visitante_" + ii).val() && i !== ii) {
                error_color("#time_visitante_" + i);
                error_color("#time_visitante_" + ii);
                valid = false;
            }

            if ($("#time_mandante_" + i).val() === $("#time_visitante_" + ii).val()) {
                error_color("#time_mandante_" + i);
                error_color("#time_visitante_" + ii);
                valid = false;
            }
        }
    }

    if (valid === true) {
        $(this)[0].submit();
    }
});

function confere_form_gerencia() {
    if ($("#btn_gerenciar_rodada").val() === "Cadastrar") {
        for (var i = 1; i <= 10; i++) {
            if ($("#time_mandante_" + i).val() != 0) {
                return 1;
            }
            if ($("#time_visitante_" + i).val() != 0) {
                return 1;
            }
            if ($("#data_partida_" + i).val() != "") {
                return 1;
            }
        }
    }

    return 0;
}

