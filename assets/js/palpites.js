$("#rodadas_cadastradas").change(function () {
    $("#msg").html("");
    var rodada = $(this).val();
    var confere = confere_form_palpite();
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
    var confere = confere_form_palpite();
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
    $.ajax({
        url: url_js("Palpites/palpites_usuario/" + rodada),
        type: "Post",
        dataType: "json"
    }).done(function (data) {
        console.log(data);
        if (data.existe_rodada === 1) {
            $("#info_rodada").html("Rodada " + rodada + " | Inicio: " + moment(data.inicio).format("ddd DD/MM HH:mm") + " | Fim: " + moment(data.fim).format("ddd DD/MM HH:mm"));
            $("table > tbody > tr").removeClass();
            
            if (form === 0) {
                $(".clear_input").val("");
            }
            
            $("#form_palpites").prop("action", url_js("Palpites/enviar_palpites/" + rodada));
            var tapostas = 0, tpontos = 0, tlucro = 0;
            
            $.each(data.detalhes_rodada, function (i, item) {
                if (data.usuario_palpitou === 1) {
                    insere_input("palpite_mandante", item.cad_partida, data['palpites'][i]['pap_gol_mandante'], item.cad_data, form);
                    insere_input("palpite_visitante", item.cad_partida, data['palpites'][i]['pap_gol_visitante'], item.cad_data, form);
                    insere_input("aposta_partida", item.cad_partida, data['palpites'][i]['pap_aposta'], item.cad_data, form);
                    tapostas += ($.isNumeric(parseInt(data['palpites'][i]['pap_aposta']))) ? parseInt(data['palpites'][i]['pap_aposta']) : 0;
                    $("#pontos_partida_" + item.cad_partida).html(data['palpites'][i]['pap_pontos']);
                    tpontos += parseInt(data['palpites'][i]['pap_pontos']);
                    $("#lucro_partida_" + item.cad_partida).html("M$ " + data['palpites'][i]['pap_lucro']);
                    tlucro += parseInt(data['palpites'][i]['pap_lucro']);
                } else {
                    insere_input("palpite_mandante", item.cad_partida, null, item.cad_data, form);
                    insere_input("palpite_visitante", item.cad_partida, null, item.cad_data, form);
                    insere_input("aposta_partida", item.cad_partida, null, item.cad_data, form);
                    $("#pontos_partida_" + item.cad_partida).html(0);
                    $("#lucro_partida_" + item.cad_partida).html("M$ 0");
                }
                
                $("#img_mandante_" + item.cad_partida).html("<img src='" + url_js("assets/images/times/" + item.cad_time_mandante_var + ".png") + "' class='img_palpite'><br><span class='nome_palpite'>" + item.cad_time_mandante + "</span>");
                $("#gol_mandante_" + item.cad_partida).html(verif_valor(item.cad_time_mandante_gol));
                $("#gol_visitante_" + item.cad_partida).html(verif_valor(item.cad_time_visitante_gol));
                $("#img_visitante_" + item.cad_partida).html("<img src='" + url_js("assets/images/times/" + item.cad_time_visitante_var + ".png") + "' class='img_palpite'><br><span class='nome_palpite'>" + item.cad_time_visitante + "</span>");
                $("#data_partida_" + item.cad_partida).html(configura_data(item.cad_data));
                $("#local_partida_" + item.cad_partida).html(item.cad_local);
                $("table > tbody > tr:eq(" + (item.cad_partida - 1) + ")").css({"background-color": data['detalhes_times'][item.cad_time_mandante_var]['first_color'], "color": data['detalhes_times'][item.cad_time_mandante_var]['second_color']});
            });

            if (data.usuario_palpitou) {
                $("#btn_palpitar").val("Atualizar");
            } else {
                $("#btn_palpitar").val("Palpitar");
            }

            if (!autoriza_palpite(data.fim)) {
                $("#btn_palpitar").prop("disabled", true);
            }

            if(form === 1){
                $("#total_mangos_palpites").html("M$ " + tapostas);
            } else{
                $("#total_mangos_palpites").html("M$ " + tapostas);
            }
            
            $("#total_pontos_palpites").html(" " + tpontos);
            $("#total_lucro_palpites").html("M$ " + tlucro);
            $("#clear_table").show();
        } else {
            $(".clear_input").val("");
            $("#info_rodada").html(data.msg);
            $("#clear_table").hide();
        }
        
        $("#rodadas_cadastradas").val(rodada);
        var anterior = (rodada > 1 && rodada <= 38) ? rodada - 1 : rodada;
        var proximo = (rodada > 0 && rodada <= 37) ? parseInt(rodada) + 1 : rodada;
        $("#anterior_rodada").val(anterior);
        $("#proximo_rodada").val(proximo);
    }).fail(function (data) {
        $("#clear_table").hide();
        $("#info_rodada").html("Houve um erro ao trazer os dados da rodada. Por favor tente mais tarde");
        $("#rodadas_cadastradas").val(rodada);
        var anterior = (rodada > 1 && rodada <= 38) ? rodada - 1 : rodada;
        var proximo = (rodada > 0 && rodada <= 37) ? parseInt(rodada) + 1 : rodada;
        $("#anterior_rodada").val(anterior);
        $("#proximo_rodada").val(proximo);
    });
}

function insere_input(id, partida, valor, data, form) {
    if (autoriza_palpite(data)) {
        if (form === 0) {
            $("#" + id + "_" + partida).val(verif_valor(valor)).prop("disabled", false).css("color", "black");
        }
    } else {
        if (valor == null) {
            $("#" + id + "_" + partida).val("X").prop("disabled", true).css("color", "red");
        } else {
            $("#" + id + "_" + partida).val(valor).prop("disabled", true).css("color", "blue");
        }
    }
}

function verif_valor(valor) {
    if (valor == null) {
        return "";
    } else {
        return valor;
    }
}

function configura_data(data) {
    return moment(data).format("ddd DD/MM H:mm");
}

function autoriza_palpite(data) {
    if (moment().diff(data, "seconds") <= 0) {
        return true;
    } else {
        return false;
    }
}

var str_p = "";
var str_a = "";
for (var i = 1; i <= 10; i++) {
    str_p += "#palpite_mandante_" + i + ", #palpite_visitante_" + i + ", ";
    str_a += "#aposta_partida_" + i + ", ";
}

$(str_a.slice(0, -2)).keyup(function () {
    soma_aposta();
});

function soma_aposta(){
    var val, total = 0;
    for (var i = 1; i <= 10; i++) {
        val = $("#aposta_partida_" + i).val();
        if ($.isNumeric(val)) {
            total += parseInt(val);
        }
    }

    $("#total_mangos_palpites").html("M$ " + total);
    return total;
}

$("#form_palpites").submit(function (e) {
    e.preventDefault();

    success_color(".input_palpite");


    var valor;
    var valid = true;
    for (var i = 1; i <= 10; i++) {

        valor = $("#palpite_mandante_" + i).val();
        if (!$.isNumeric(valor) || Math.floor(valor) != valor) {
            if (valor != "X") {
                error_color("#palpite_mandante_" + i);
                valid = false;
            }
        }
        valor = $("#palpite_visitante_" + i).val();
        if (!$.isNumeric(valor) || Math.floor(valor) != valor) {
            if (valor != "X") {
                error_color("#palpite_visitante_" + i);
                valid = false;
            }
        }
        valor = $("#aposta_partida_" + i).val();
        if (valor && !$.isNumeric(valor) || Math.floor(valor) != valor) {
            if (valor != "X") {
                error_color("#aposta_partida_" + i);
                valid = false;
            }
        }
    }

    if (valid) {
        $(this)[0].submit();
    } else{
        $("#focus_msg").focus();
        $("#msg").html("Ops, existe um erro nos seus palpites. Insira apenas números e é obrigatório inserir os gols nas partidas que não começaram. Desce lá e veja onde errou ;)");
    }
});

function confere_form_palpite() {
    if ($("#btn_palpitar").val() === "Palpitar") {
        for (var i = 1; i <= 10; i++) {
            if ($("#palpite_mandante_" + i).val() != "") {
                return 1;
            }
            if ($("#palpite_visitante_" + i).val() != "") {
                return 1;
            }
            if ($("#aposta_partida_" + i).val() != "") {
                return 1;
            }
        }
    }

    return 0;
}
