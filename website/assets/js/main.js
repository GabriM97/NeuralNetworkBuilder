$(document).ready(function(){
  home_main();
});

function home_main() {
  homePage();
  addEventsOnButtons();
  $("#back_home").on("click", function(){ home_main(); });
}

// --- BUTTONS LISTENER ---
function addEventsOnButtons(){
  $(".home_btn").on("click", function(){
    if($(this).attr("name") == "new-network_btn"){
      createNeuralNewtwork();
    }else
    if($(this).attr("name") == "import-network_btn"){
      importNeuralNewtwork();
    }
  });
}

// <button type="button" class="btn btn-outline-dark btn-lg btn-block">Create new Neural Network</button>\
// <button type="button" class="btn btn-outline-dark btn-lg btn-block">Import pre-trained Neural Network</button>\

// --- INIT PAGE ---
function homePage(){
  $("#container").empty();

  var content = '\
    <div id="home_content">\
      <div id="main-title_container"></div>\
      <div id="home_main-content">\
        <button type="button" name="new-network_btn" class="home_btn">Create new Neural Network</button>\
        <button type="button" name="import-network_btn" class="home_btn">Import pre-trained Neural Network</button>\
      </div>\
    </div>';

   $("#container").append(content);
   $("#main-title_container").append(main_title);
   var sub_title = "Choose if you want to build a <b>new</b> Neural Network or if you want to <b>import</b> a pre-trained Neural Network";
   $("#main-title_container #sub-title").html(sub_title);
}

var main_title = '\
  <div>\
    <a href="#" id="back_home">Home</a>\
    <div id="github-logo">\
      <a href="https://github.com/GabriM97/NeuralNetworkBuilder" target="_blank" title="Repository GitHub">\
        <img src="assets/img/github-logo.jpg" alt="GitHub Logo">\
      </a>\
    </div>\
    <h1 id="main-title">Neural Network Builder</h1>\
    <h3 id="sub-title"></h3>\
  </div>';
