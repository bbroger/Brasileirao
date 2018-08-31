$("#rodadas_cadastradas").change(function (){
    consultar_rodada($(this).val());
});

$("#anterior_rodada, #proximo_rodada").click(function (){
    var rodada= $(this).val();
    consultar_rodada(rodada);
});

function consultar_rodada(rodada, form= null) {
    console.log(form);
    if (!$.isNumeric(rodada) || Math.floor(rodada) != rodada) {
        rodada = 1;
    }
    $.ajax({
        url: ajax("Palpites/palpites_usuario/" + rodada),
        type: "Post",
        dataType: "json"
    }).done(function (data) {
        if (data.existe_rodada === 1) {
            $("#info_rodada").html("Rodada " + rodada + " | Inicio: " + moment(data.inicio).format("DD/MM HH:mm") + " | Fim: " + moment(data.fim).format("DD/MM HH:mm"));
            $("table > tbody > tr").removeClass();
            $(".clear_input").val("");
            $("#clear_table").show();
            
            $.each(data.palpites_completo, function (i, item) {
                $("#img_mandante_"+item.cad_partida).html("<img src='"+ajax("assets/images/times/"+item.cad_time_mandante_id+".png")+"' class='img_palpite'><br><span class='nome_palpite'>"+item.cad_time_mandante+"</span>");
                insere_input("palpite_mandante", item.cad_partida, item.pap_gol_mandante, item.cad_data);
                insere_input("palpite_visitante", item.cad_partida, item.pap_gol_visitante, item.cad_data);
                $("#gol_mandante_"+item.cad_partida).html(verif_valor(item.cad_time_mandante_gol));
                $("#gol_visitante_"+item.cad_partida).html(verif_valor(item.cad_time_visitante_gol));
                $("#img_visitante_"+item.cad_partida).html("<img src='"+ajax("assets/images/times/"+item.cad_time_visitante_id+".png")+"' class='img_palpite'><br><span class='nome_palpite'>"+item.cad_time_visitante+"</span>");
                insere_input("aposta_partida", item.cad_partida, item.pap_aposta, item.cad_data);
                $("#pontos_partida_"+item.cad_partida).html(item.pap_pontos);
                $("#lucro_partida_"+item.cad_partida).html("M$ "+item.pap_lucro);
                $("#data_partida_"+item.cad_partida).html(configura_data(item.cad_data));
                $("#local_partida_"+item.cad_partida).html(item.cad_local);
                $("table > tbody > tr:eq("+(item.cad_partida-1)+")").addClass(item.cad_time_mandante_id);
            });
        } else {
            $("#info_rodada").html("Rodada "+rodada+" não cadastrada");
            $("#clear_table").hide();
        }
        $("#rodadas_cadastradas").val(rodada);
        var anterior= (rodada > 1 && rodada <=38) ? rodada-1 : rodada;
        var proximo= (rodada > 0 && rodada <=37) ? parseInt(rodada) + 1 : rodada;
        $("#anterior_rodada").val(anterior);
        $("#proximo_rodada").val(proximo);
    }).fail(function (data) {
    });
}

function insere_input(id, partida, valor, data){
    if(autoriza_palpite(data)){
        if($("#"+id+"_"+partida).val() == ""){
            $("#"+id+"_"+partida).val(verif_valor(valor));
        }
    } else{
        if($("#"+id+"_"+partida).val() == ""){
            $("#"+id+"_"+partida).val(verif_valor(valor)).prop("disabled", true);
        }
    }
}

function verif_valor(valor){
    if(valor == null){
        return "";
    } else{
        return valor;
    }
}

function configura_data(data){
    return moment(data).format("DD/MM H:mm");
}

function autoriza_palpite(data){
    if(moment().diff(data, "seconds") <= 0){
        return true;
    } else{
        return false;
    }
}
