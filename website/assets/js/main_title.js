function mainTitleInit(sub_title){
  var main_title = '\
      <a href="./" id="back_home">Home</a>\
      <div id="github-logo">\
        <a href="https://github.com/GabriM97/NeuralNetworkBuilder" target="_blank" title="Repository GitHub">\
          <img src="assets/img/github-logo.png" alt="GitHub Logo">\
        </a>\
      </div>\
      <h1 id="main-title">Neural Network Builder</h1>\
      <h3 id="sub-title"></h3>';

  $("#container").prepend("<div id='main-title_container'></div>");
  $("#main-title_container").append(main_title);
  $("#main-title_container #sub-title").html(sub_title);
  //$("#back_home").on("click", function(){ home_main(); });
}
