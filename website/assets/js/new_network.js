// --- CREATE NEW NEURAL NETWORK ---
function createNeuralNewtwork() {
  $("#container").empty();

  var sub_title = "Create a <b>new</b> Neural Network. &nbsp;From zero to <b>Hero</b>!";
  mainTitleInit(sub_title);

  var content = '\
  <div id="new-network_content">\
    <div id="form-container">\
      <form id="new-network_form" action="new_network.php" method="POST" enctype="multipart/form-data"></form>\
    </div>\
  </div>';
  $("#container").append(content);

  addSections();
  addButtons();
}

function addSections(){
  addDataset();
  addBuildModel();
  addLayers();
  addCompileModel();
  addTrainModel();
  addEvaluateModel();
}

row_cnt = 0;

function addDataset(){
  var dataset = '\
    <div id="dataset-section" class="col-5">\
      <p class="section-title">Dataset</p>\
      <div class="col-6">\
        <div class="elem-center">\
          Choose Dataset file:<br>\
          <input type="file" id="import_dataset" name="import_dataset" accept=".json, .csv, .pickle, .pkl">\
        </div>\
      </div>\
      <div class="col-6">\
    		<div class="elem-center">\
    			Input Dimension<br>\
    			<input type="number" id="input_shape" name="input_shape" value="1" min="1">\
    		</div>\
      </div>\
    </div>';

    $("#new-network_form").append("<div id='row" + row_cnt + "' class='row'></div>");
    $("#row"+row_cnt).append(dataset);
    row_cnt++;
}

function addBuildModel(){
  $("#dataset-section").after("<div id='build-section' class='col-7'></div>");
  $("#build-section").append("<p class='section-title'>Build Model</p>");
  $("#build-section").append("<div id='row" + row_cnt + "' class='row'></div>");

  var model_type = '\
  <div class="col-4">\
    <div class="elem-center">\
      Model type <br>\
      <select name="model_type">\
        <option value="seq" selected>Sequential</option>\
        <option value="func" disabled>Functional</option>\
      </select>\
    </div>\
  </div>';

  var layers_num = '\
  <div class="col-4">\
    <div class="elem-center">\
      Layers number <br>\
      <input type="number" id="layers_number" name="layers_number" value="3" min="1" max="20">\
    </div>\
  </div>';

  var output_classes = '\
  <div class="col-4">\
    <div class="elem-center">\
      Output classes <br>\
      <input type="number" id="output_classes" name="output_classes" value="2" min="1" max="100">\
    </div>\
  </div>';

  $("#row"+row_cnt).append(model_type);
  $("#row"+row_cnt).append(layers_num);
  $("#row"+row_cnt).append(output_classes);

  $("#output_classes").change(function(){
      var value = $(this).val();
      $("#layers_container [id*='_" + (content_cnt-1) + "'] [name*='neur_number']").val(value);
  });

  row_cnt++;
}

function addLayers(){
  // GET LAYERS NUMBER
  var layers_num = $("#build-section [name='layers_number']").val();
  $(document).on('input', '#layers_number', function(){
    var layers_num = $(this).val();
    renderLayers(layers_num);
  });

  $("#new-network_form").append("<div id='layers_container'></div>");
  renderLayers(layers_num);
}

var content_cnt = 0;
function renderLayers(layers_num){
  $("#layers_container").empty();
  content_cnt = 0;

  var layers_per_row = 4;
  var cols_full = 12/layers_per_row;

  var num_of_main_rows = layers_num > layers_per_row ? parseInt(layers_num/layers_per_row)+1 : 1;
  var last_row_layers = layers_num % layers_per_row;
  if(layers_num%layers_per_row != 0)   var cols_last_layer = 12/last_row_layers;
  else    var cols_last_layer = cols_full;
  var main_row_cnt = 0;

  $("#layers_container").append("<div id='layer-row" + main_row_cnt + "' class='row'></div>");
  main_row_cnt++;

  for(var i=0; i<layers_num; i++){
    if(main_row_cnt == num_of_main_rows){
      // last row
      addLayer_col(cols_last_layer, main_row_cnt-1, i);
    }else{
      addLayer_col(cols_full, main_row_cnt-1, i);
      if(((i+1)%layers_per_row) == 0 && i!=layers_num-1){   //next step is new row
        $("#layers_container").append("<div id='layer-row" + main_row_cnt + "' class='row'></div>");
        main_row_cnt++;
      }
    }
  }

  var last_layer_selector = "#layers_container [id*='_" + (content_cnt-1) + "'] [name*='neur_number']";
  var value = $("#output_classes").val();
  $(last_layer_selector).val(value);

  $("#layers_container [id*='_" + (content_cnt-1) + "'] [name*='neur_number']").change(function(){
    var value = $(this).val();
    $("#output_classes").val(value);
  });
}

function addLayer_col(cols, current_main_row, layer_number_cnt){
  var layer_content = $("<div class='layer col-" + cols + "'></div>");
  var layer_name = "<p class='layer-num'>Layer " + layer_number_cnt + "</p>";
  $(layer_content).append(layer_name);
  $(layer_content).append("<div id='row-" + current_main_row + "_" + content_cnt + "' class='row'></div>");
  content_cnt++;
  $("#layer-row"+current_main_row).append(layer_content);

  var neurons_num = '\
  <div class="col-6">\
    <div class="elem-center">\
      Neurons <br>\
      <input type="number" name="neur_number[]" value="4" min="1" max="100">\
    </div>\
  </div>';
  $("#row-" + current_main_row + "_" + (content_cnt-1)).append(neurons_num);

  var activ_func = '\
  <div class="col-6">\
    <div class="elem-center">\
      Activation function <br>\
      <select name="activ_funct[]">\
        <option value="relu" selected>ReLU</option>\
        <option value="sigmoid">Sigmoid</option>\
        <option value="tanh">Tanh</option>\
        <option value="linear">Linear</option>\
        <option value="softmax">Softmax</option>\
      </select>\
    </div>\
  </div>';
  $("#row-" + current_main_row + "_" + (content_cnt-1)).append(activ_func);
}

function addCompileModel(){
  $("#new-network_form").append("<div id='row" + row_cnt + "' class='row'></div>");
  $("#row" + row_cnt).append("<div id='compile-section' class='col-6'></div>");
  row_cnt++;
  $("#compile-section").append("<p class='section-title'>Compile Model</p>");

  var learning_rate = '\
  <div class="col-4">\
    <div class="elem-center">\
      Learning rate <br>\
      <select name="learning_rate">\
        <option value="0.0001">0.0001</option>\
        <option value="0.001">0.001</option>\
        <option value="0.003">0.003</option>\
        <option value="0.01" selected>0.01</option>\
        <option value="0.03">0.03</option>\
        <option value="0.1">0.1</option>\
        <option value="0.3">0.3</option>\
        <option value="1">1</option>\
        <option value="3">3</option>\
        <option value="10">10</option>\
      </select>\
    </div>\
  </div>';
  $("#compile-section").append(learning_rate);

  var optimizer = '\
  <div class="col-4">\
    <div class="elem-center">\
      Optimizer <br>\
      <input type="radio" name="optimizer" value="adam" checked="checked">Adam\
      <br>\
      <input type="radio" name="optimizer" value="sgd">SGD\
    </div>\
  </div>';
  $("#compile-section").append(optimizer);

  var metrics = '\
  <div class="col-4">\
    <div class="elem-center">\
      Metrics list <br>\
      <input type="checkbox" name="metrics" value="accuracy" checked="checked">Accuracy\
    </div>\
  </div>';
  $("#compile-section").append(metrics);

}

function addTrainModel(){
  $("#compile-section").after("<div id='train-section' class='col-6'>");
  $("#train-section").append("<p class='section-title'>Train Model</p>");

  var epochs = '\
  <div class="col-4">\
    <div class="elem-center">\
      Epochs <br>\
      <input type="number" name="epochs" value="5" min="1" max="1000">\
    </div>\
  </div>';
  $("#train-section").append(epochs);

  var batch_size = '\
  <div class="col-4">\
    <div class="elem-center">\
      Batch size <br>\
      <input type="number" name="batch_size" value="8" min="2" max="256">\
    </div>\
  </div>';
  $("#train-section").append(batch_size);

  var valid_split = '\
  <div class="col-4">\
    <div class="elem-center">\
      Validation-Set split <br>\
      <input type="number" name="validation_split" value="0.0" min="0" max="1" step="0.01">\
    </div>\
  </div>';
  $("#train-section").append(valid_split);
}

function addEvaluateModel(){
  $("#new-network_form").append("<div id='row" + row_cnt + "' class='row'></div>");
  $("#row" + row_cnt).append("<div id='evaluate-section' class='col-12'></div>");
  row_cnt++;

  var evaluate = '\
  <p class="section-title">Evaluate Model</p>\
  <p class="question">Do you want to evaluate your model, after training it?</p>\
  <div class="answer">\
    <input type="radio" name="evaluate_choose" value="true" checked="checked">Yes\
    <input type="radio" name="evaluate_choose" value="false">No\
  </div>';
  $("#evaluate-section").append(evaluate);
}

function addButtons(){
  $("#new-network_form").append("<div id='row" + row_cnt + "' class='row'></div>");
  $("#row" + row_cnt).append("<div id='form-buttons' class='col-12'></div>");
  row_cnt++;

  var buttons = '\
  <div class="form_btn">\
    <button type="submit" id="submit_btn" name="submit-btn" class="new-network_btn">Submit</button>\
  </div>\
  <div class="form_btn">\
    <button type="reset" id="reset_btn" name="reset-btn" class="new-network_btn">Reset all</button>\
  </div>';

  $("#form-buttons").append(buttons);

  //$("#submit_btn").on("click", function(){ buildModelMain(); });

}
