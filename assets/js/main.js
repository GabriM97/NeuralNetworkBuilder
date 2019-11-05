$(document).ready(function(){
  home_main();
});

function home_main() {
  homePage();
  // buildModelMain();
  // saveInputData();
  addEventsOnMainPageButtons();
}

// --- BUTTONS LISTENER ---
function addEventsOnMainPageButtons(){
  $(".home_btn").on("click", function(){
    if($(this).attr("name") == "new-network_btn"){
      createNeuralNewtwork();
    }else
    if($(this).attr("name") == "import-network_btn"){
      importNeuralNewtwork();
    }
  });
}

// --- INIT PAGE ---
function homePage(){
  $("#container").empty();

  var sub_title = "Choose if you want to build a <b>new</b> Neural Network or if you want to <b>import</b> a pre-trained Neural Network";
  mainTitleInit(sub_title);
  $("#back_home").addClass("active");

  var content = '\
    <div id="home_content">\
      <button type="button" name="new-network_btn" class="home_btn">Create new Neural Network</button>\
      <button type="button" name="import-network_btn" class="home_btn">Import pre-trained Neural Network</button>\
    </div>';

   $("#container").append(content);
}
