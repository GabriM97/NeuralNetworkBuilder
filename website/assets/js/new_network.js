// --- CREATE NEW NEURAL NETWORK ---
function createNeuralNewtwork() {
  $("#container").empty();

  var content = '\
    <div id="new-network_content">\
      <div id="main-title_container"></div>\
      <div id="new-network_main-content"></div>\
    </div>';

  var sub_title = "Create a <b>new</b> Neural Network. &nbsp;From zero to <b>Hero</b>!";

  $("#container").append(content);
  $("#main-title_container").append(main_title);
  $("#main-title_container #sub-title").html(sub_title);

  $("#back_home").on("click", function(){ home_main(); });

  new_network_content();
}

function new_network_content() {
  var content = '\
    <div id="form-container">\
      <form id="new-network_form" action="new_network.php" method="post"></form>\
    </div>';
  $("#new-network_main-content").append(content);

  addSections();
  // addButtons();
}

function addSections(){
  addDataset();
  addBuildModel();
  addLayers();
  // addCompileModel();
  // addTrainModel();
  // addEvaluateModel();
}

row_cnt = 0;

function addDataset(){
  var dataset = '\
    <div id="dataset-section" class="col-5">\
      <p class="section-title">Dataset</p>\
      <div class="elem-center">\
        Choose Dataset file:<br>\
        <input type="file" name="import_dataset" accept=".h5">\
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
      <input type="number" name="output_classes" value="2" min="2" max="20">\
    </div>\
  </div>';

  $("#row"+row_cnt).append(model_type);
  $("#row"+row_cnt).append(layers_num);
  $("#row"+row_cnt).append(output_classes);

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
}

var content_cnt = 0;
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
      <input type="number" name="neur_number" value="4" min="1" max="100">\
    </div>\
  </div>';
  $("#row-" + current_main_row + "_" + (content_cnt-1)).append(neurons_num);

  var activ_func = '\
  <div class="col-6">\
    <div class="elem-center">\
      Activation function <br>\
      <select name="activ_funct">\
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
