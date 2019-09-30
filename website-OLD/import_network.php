<?php  $python_path = "C:/Users/Gabriele/AppData/Local/Programs/Python/Python37/python.exe";   ?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <link rel="stylesheet" type="text/css" href="assets/css/main-title_style.css">
    <link rel="stylesheet" type="text/css" href="assets/css/new_network-php_page.css">
    <meta charset="utf-8">
    <title>NNB - Build Model</title>
  </head>
  <script type="text/javascript" src="assets/js/lib/jquery-3.4.1.js"></script>
  <script type="text/javascript" src="assets/js/main_title.js"></script>
  <body>
    <div id="container">
      <?php
         $jsScript =
       '<script type="text/javascript">
          var sub_title = "<b>Output</b> value in the box <i>below</i>!";
          mainTitleInit(sub_title);
        </script>';
        echo $jsScript;
      ?>
      <div id="terminal-container">
        <div id="terminal-content">
          <pre class="pre-container">
          <?php

              // SAVE MODEL
              $model_tmp_name = $_FILES["import_model"]["tmp_name"];
              $model_name = $_FILES["import_model"]["name"];
              echo "\n <pre class='pre-title'> \tStep 1: -- SAVE MODEL -- </pre>";
              $model = saveModel($model_tmp_name, $model_name);

              // PREDICT INPUT
              $input_values = $_POST["input_values"];
              echo "<hr> <pre class='pre-title'> \tStep 2: -- PREDICT INPUT -- </pre>";
              $exit = predictInput($input_values);

              $autoscroll =
              '<script type="text/javascript">
                var height = 0;
                $("pre").each(function(i, value){
                  height += parseInt($(this).height());
                });
                height += \'\';
                $("#terminal-container").animate({scrollTop: height});
              </script>';
              echo $autoscroll;
            ?>
          </pre>
        </div>
      </div>
    </div>
  </body>
  <script type="text/javascript" src="assets/js/lib/jquery-3.4.1.js"></script>
  <script type="text/javascript" src="assets/js/main_title.js"></script>
  <script type="text/javascript">
    // function consoleText(words, elem){
    //   var letterCount = 1;
    //   var x = 1;
    //   var target = document.getElementsByTagName(elem).lastChild;
    //   //var target = $(id+":last-child");
    //   window.setInterval(function(){
    //     target.innerHTML = words.substring(0, letterCount)
    //     //target.html(words.substring(0, letterCount));
    //     letterCount += x;
    //   }, 100);
    // }
  </script>
</html>


<?php
  /* --- SAVE MODEL --- */
  function saveModel($model_tmp_name, $model_name){
    $model_original_name = $model_name;
    $model_name = "client_model.h5";

    $local_path = "./python/saves/" . $model_name;
    if(move_uploaded_file($model_tmp_name, $local_path)){
      echo "<pre class='pre-content'>Model $model_original_name saved!</pre>";
      return $model_name;
    }else{
        echo "Failed to upload .";
        return -1;
    }
  }

  /* --- PREDICT INPUTS --- */
  function predictInput($input_data){
    global $python_path;
    $cmd = escapeshellcmd("$python_path ./python/import_model.py $input_data");

    $exit_status = exec_script($cmd);
    if($exit_status == -1)
      echo "<br>ERROR PREDICTING ON INPUT ...";

    return $exit_status;
  }


  function exec_script($cmd){
    while (@ ob_end_flush());   // end all output buffers if any
    $proc = popen($cmd, 'r');

    $live_output = "";
    $all_output = "";
    echo "<pre class='pre-content'>";

    while(!feof($proc)){
        $live_output = fread($proc, 1);     //read each 1 Byte
        $all_output = $all_output . $live_output;
        echo $live_output;
        //echo "<script type='text/javascript'> consoleText('$live_output', 'pre'); </script>";
        @ flush();
    }
    echo "</pre>";
    pclose($proc);

    if($exit_status = strstr($all_output, "exit_status"))
      return substr($exit_status, -2, -1);
    else
      return -1;
  }

  // function test_cmd(){
  //   global $python_path;
  //   $command = escapeshellcmd("$python_path ./python/builder.py");
  //   system($command);
  // }
?>
