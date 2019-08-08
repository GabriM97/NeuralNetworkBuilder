$(document).ready(function(){
  home_main();
});

function home_main() {
  homePage();
  // buildModelMain();
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

// <button type="button" class="btn btn-outline-dark btn-lg btn-block">Create new Neural Network</button>\
// <button type="button" class="btn btn-outline-dark btn-lg btn-block">Import pre-trained Neural Network</button>\

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

function mainTitleInit(sub_title){
  var main_title = '\
      <a href="#home" id="back_home">Home</a>\
      <div id="github-logo">\
        <a href="https://github.com/GabriM97/NeuralNetworkBuilder" target="_blank" title="Repository GitHub">\
          <img src="assets/img/github-logo.jpg" alt="GitHub Logo">\
        </a>\
      </div>\
      <h1 id="main-title">Neural Network Builder</h1>\
      <h3 id="sub-title"></h3>';

  $("#container").append("<div id='main-title_container'></div>");
  $("#main-title_container").append(main_title);
  $("#main-title_container #sub-title").html(sub_title);
  $("#back_home").on("click", function(){ home_main(); });

}
