var interval;

$(document).ready(function(){

    var updateDataUrl = window.location.href + "/getTrainingInfo";

    if($("#in_queue").attr("value") === "1" )    //if training is processing
        interval = setInterval(getData, 500, updateDataUrl);
});

function getData(updateDataUrl) {
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
}

function setData(data){
    if($("#train_status").attr("value") !== data["status"])
        location.reload(true);

    $("#in_queue").attr("value", data["in_queue"]);
    $("#train_status").attr("value", data["status"]);
    $("#train_perc").text(Math.round(data["train_perc"]*100))
    $("#acc_val").text(Math.round(data["accuracy"]*100));
    $("#loss_val").text(Math.round(data["loss"]*100));
}