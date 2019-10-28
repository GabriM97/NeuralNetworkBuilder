var interval;

$(document).ready(function(){

    var updateDataUrl = window.location.href + "/getTrainingInfo";

    if($("#in_queue").attr("value") === "1" )    //if training is processing
        interval = setInterval(getData, 500, updateDataUrl);
});

function getData(updateDataUrl) {
    //var current_train_perc = $("#train_perc").text();
    //var current_acc_val = $("#acc_val").text();
    //var current_loss_val = $("#loss_val").text();

    var csrf_token = $("input[name='_token']").val();

    fetch(updateDataUrl, {
        method : 'post',
        headers: {
            'Content-Type': 'application/json',  // sent request
            'Accept':       'application/json'   // expected data sent back
          },
        body: JSON.stringify({_token: csrf_token,})
    })
    .then((res) => res.json())
    .then((data) => setData(data))
    .catch((error) => console.log(error))

    if($("#in_queue").attr("value") === "0" ){
        clearInterval(interval);
        location.reload(true);
    }
}

function setData(data){
    $("#in_queue").attr("value", data["in_queue"]);
    $("#train_status").text(getStatus(data["status"]));
    $("#train_perc").text(data["train_perc"]*100)
    $("#acc_val").text(data["accuracy"]*100);
    $("#loss_val").text(data["loss"]*100);
}

function getStatus(status) {
    if(status == "started"){        
        $("#train_status").parent().attr("class", "").addClass("text-primary");
        return "In Progress";
    }else if(status == "paused"){
        $("#train_status").parent().attr("class", "").addClass("text-info");
        return "In Pause";
    }
    
}