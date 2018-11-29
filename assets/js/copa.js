function consultar_copa(rodada, copa, liga){
    console.log(liga);
    $.ajax({
        url: url_js("Copa/recebe_dados_copa/" + rodada + "/" + copa + "/" + liga),
        type: "Post",
        dataType: "json"
    }).done(function (data) {
        console.log(data);
        $("#nome_copa").html(data["nome"]);
        $("#rodada_copa").html(data["rodada"]);
        $("#data_copa").html(data["data"]);
        for(var i=1; i <= 16; i++){
            $("#oitavas_"+i).html(data.oitavas[i]["mostra"]);
            if(i <= 8){
                $("#quartas_"+i).html(data.quartas[i]["mostra"]);
            }
            if(i <= 4){
                $("#semi_"+i).html(data.semi[i]["mostra"]);
            }
            if(i <= 2){
                $("#final_"+i).html(data.final[i]["mostra"]);
            }
        }
        $("#campeao").html(data.campeao[1]["mostra"]);
        $("#inscritos").html(data["inscritos"]);
        $("#premiacao").html(data["premiacao"]);
    }).fail(function (data) {
        console.log(data);
    });
}

