moment.locale('pt-BR');

function error_color(input, label = null, id_msg = null, msg = null){
    $(input).css({"border" : "1px solid red", "color" : "red"});
    if(label !== null){
        $(label).css("color", "red");
    }
    if(id_msg !== null){
        $(id_msg).css("color", "red");
    }
    if(msg !== null){
        $(id_msg).html(msg);
    }
}

function success_color(input, label= null, id_msg= null, msg = null){
    $(input).css({"border" : "1px solid #ccc", "color" : "#555"});
    if(label !== null){
        $(label).css("color", "#333");
    }
    if(id_msg !== null){
        $(id_msg).css("color", "#737373"); 
    }
    if(msg !== null){
        $(id_msg).html(msg);
    }
}