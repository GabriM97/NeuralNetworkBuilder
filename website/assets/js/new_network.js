
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



}
