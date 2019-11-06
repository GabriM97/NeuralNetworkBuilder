$(document).ready(function(){
        disableDataset($("#model_id"), true);

    $("#model_id").change(function(){
        disableDataset(this, false);
        
        //if not exists dataset training selected option
        if(!$("#training_dataset option:selected").length){
            /*setTimeout(function() { // render HTML before run the alert
                alert("No matching dataset found! \nImport a new matching dataset or change the model.");
                },0);*/
            error_msg = '\
            <span class="invalid-feedback" role="alert">\
                <strong>No matching dataset found! Import a new matching dataset or change the model.</strong>\
            </span>';
            //$("#training_dataset").after(error_msg);
        }
    });
});

function disableDataset(model, first_run){
    var selectedModel = $(model).children("option:selected");
    x = $(selectedModel).attr("x_inp");
    y = $(selectedModel).attr("y_out");

    //for each training dataset option
    $("#training_dataset option").map(function(){
        x_data = $(this).attr("x_inp");
        y_data = $(this).attr("y_out");
        if(x_data != x || y_data != y){
            $(this).attr("disabled", true).addClass('d-none');
            $(this).attr('selected', false);
        }else{  // x_data == x && y_data == y
            if(!$('input[name="_method"]').length){     // new training
                $("#training_dataset").children("option:selected").attr('selected', false); //remove previously selected option
                $(this).attr("disabled", false).removeClass('d-none');
                $(this).attr('selected', true);
            }else{      // edit training
                if(first_run)
                    $(this).attr("disabled", false).removeClass('d-none');
                else{   // model on change
                    $("#training_dataset").children("option:selected").attr('selected', false); //remove previously selected option
                    $(this).attr("disabled", false).removeClass('d-none');
                    $(this).attr('selected', true);
                }
            }
        }
    });    

    //for each test dataset option
    $("#test_dataset option").map(function(){
        if(!$(this).val())	return

        x_data = $(this).attr("x_inp");
        y_data = $(this).attr("y_out");
        if(x_data != x || y_data != y){
            $(this).attr("disabled", true).addClass('d-none');
            $(this).attr('selected', false);
        }else{  // x_data == x && y_data == y
            if(!$('input[name="_method"]').length){     // new training
                $("#test_dataset").children("option:selected").attr('selected', false); //remove previously selected option
                $(this).attr("disabled", false).removeClass('d-none');
                $(this).attr('selected', true);
            }else{      // edit training
                if(first_run)
                    $(this).attr("disabled", false).removeClass('d-none');
                else{   // model on change
                    $("#test_dataset").children("option:selected").attr('selected', false); //remove previously selected option
                    $(this).attr("disabled", false).removeClass('d-none');
                    $(this).attr('selected', true);
                }
            }
        }
    });
}