function setMarkTo(div, value, input){
    var width = 175;
    $("#" + div + " .mark-star-value").css("width", width * value / 5 + "px");
    $("#" + input).val(value);
}

function setMarkValue(div, e, input){
    var object = $("#"+div);
    var width = 175;
    var mouseX = e.clientX-$(object).offset().left;
    var score = (mouseX/width) * 5;
    var scoreIntPart = parseInt(score);

    console.log(mouseX);

    if(score-scoreIntPart<=0.5) {
        score=scoreIntPart+0.5;
    }
    else{
        if(score-scoreIntPart<=1){
            score=scoreIntPart+1}
    }

    if(score<1){
        score=1;
    }

    $("#"+div+" .mark-star-value").css("width", width * score / 5 + "px");
    $("#" + input).val(score);
}