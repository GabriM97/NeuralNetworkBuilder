$(document).ready(function(){
  homePage();
  addEventsOnButtons();
});

// --- INIT PAGE ---
function homePage(){
  $("#container").empty();

  var content = '\
    <div id="home_content">\
      <div id="home-title_container">\
        <div id="github-logo">\
          <a href="#"><img src="assets/img/github-logo.jpg" alt="GitHub Logo"></a>\
        </div>\
        <h1 id="main-title">Neural Network Builder</h1>\
        <h3 id="sub-title">Choose if you want to build a <b>new</b> Neural Network or if you want to <b>import</b> a pre-trained Neural Network</h3>\
      </div>\
      <button type="button" name="new-network_btn" class="home_btn">Create new Neural Network</button>\
      <button type="button" name="import-network_btn" class="home_btn">Import pre-trained Neural Network</button>\
    </div>';

  $("#container").append(content)
}
