// --- IMPORT PRE-TRAINED NEURAL NETWORK ---
function importNeuralNewtwork() {

  $("#container").empty();

  var sub_title = "Import a trained <b>Model</b>, insert the values and see the <b>magic</b>!";
  mainTitleInit(sub_title);

  var content = '\
  <div id="import-model_content">\
    <div id="form-container">\
      <form id="import-model_form" action="import_network.php" method="POST" enctype="multipart/form-data"></form>\
    </div>\
  </div>';
  $("#container").append(content);

  addImportModel();
  addBttns();
}

function addImportModel() {
  var model = '\
  <div id="model-section">\
    <p class="section-title">Trained Model</p>\
    <div class="col-12">\
      <div class="import_elem-center">\
        Choose Model file:<br>\
        <input type="file" id="import_model" name="import_model" accept=".h5">\
      </div>\
      <div class="import_elem-center">\
        Input Values<br>\
        <textarea rows="3" cols="40" name="input_values" placeholder="Insert input values separated by commas: ex. 1,2,3 ..."></textarea>\
      </div>\
    </div>\
  </div>';

  $("#import-model_form").append("<div id='import_model' class='row'></div>");
  $("#import_model").append(model);
}

function addBttns() {
  $("#import-model_form").append("<div id='buttons-container' class='row'></div>");
  $("#buttons-container").append("<div id='form-buttons' class='col-12'></div>");

  var buttons = '\
  <div class="form_btn">\
    <button type="submit" id="submit_btn" name="submit-btn" class="new-network_btn">Submit</button>\
  </div>\
  <div class="form_btn">\
    <button type="reset" id="reset_btn" name="reset-btn" class="new-network_btn">Reset all</button>\
  </div>';

  $("#form-buttons").append(buttons);
}
