$("#id_copa").change(function () {
    var id_copa= $(this).val();
    if($.isNumeric(id_copa)){
        id_copa= 'copaliga';
    }
    
    var text= "";
    if(id_copa == 'copaliga'){
        for(var i= 1; i <= 33; i+=4){
            text+= "<option value='"+i+"'>Rodada "+i+"</option>";
        }
    } else{
        for(var i= 4; i <= 32; i+=4){
            text+= "<option value='"+i+"'>Rodada "+i+"</option>";
        }
    }
    $("#rodada_id_copa").html(text);
});

$("#pesquisa_id_copa").click(function (){
    var id_copa= $("#id_copa").val();
    var rodada= $("#rodada_id_copa").val();
    var liga= null;
    
    if($.isNumeric(id_copa)){
        liga= id_copa;
        id_copa= 'copaliga';
    }
    
    consultar_copa(rodada, id_copa, liga);
});

function consultar_copa(rodada, copa, liga){
    $.ajax({
        url: url_js("Copa/recebe_dados_copa/" + rodada + "/" + copa + "/" + liga),
        type: "Post",
        dataType: "json"
    }).done(function (data) {
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
        $("#focus_copa").focus();
    }).fail(function (data) {
        console.log(data);
    });
}

