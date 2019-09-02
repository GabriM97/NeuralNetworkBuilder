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
          var sub_title = "<b>Building</b> and <b>Training</b> your new <i>Neural Network</i>!";
          mainTitleInit(sub_title);
        </script>';
        echo $jsScript;
      ?>
      <div id="terminal-container">
        <div id="terminal-content">
          <pre class="pre-container">
          <?php
              $errors = "False";
              // DATASET SECTION
              $dataset_tmp_name = $_FILES["import_dataset"]["tmp_name"];
              $dataset_name = $_FILES["import_dataset"]["name"];
              $input_shape = $_POST["input_shape"];
              echo "\n <pre class='pre-title'> \tStep 1: -- SAVE DATASET -- </pre>";
              $dataset = saveDataset($dataset_tmp_name, $dataset_name);
              if($dataset != 0) $errors = "True";

              // BUILD MODEL SECTION
              $model_type = $_POST["model_type"];
              $layers_number= $_POST["layers_number"];
              $output_classes = $_POST["output_classes"];

                  // LAYERS SECTION
              $layers = array("neurons_number" => array(), "activ_function" => array());
              for($i = 0; $i < $layers_number; $i++){
                $layers["neurons_number"][$i] = $_POST["neur_number"][$i];
                $layers["activ_function"][$i] = $_POST["activ_funct"][$i];
              }
              echo "<hr> <pre class='pre-title'> \tStep 2: -- BUILD MODEL -- </pre>";
              $exit = buildModel($model_type, $layers_number, $output_classes, $input_shape, $layers);
              if($exit != 0) $errors = "True";

              // COMPILE MODEL SECTION
              $learning_rate = $_POST["learning_rate"];
              $optimizer = $_POST["optimizer"];
              $metrics = $_POST["metrics"];
              echo "<hr> <pre class='pre-title'> \tStep 3: -- COMPILE MODEL -- </pre>";
              $exit = compileModel($optimizer, $learning_rate, $output_classes, $metrics);
              if($exit != 0) $errors = "True";


              // TRAIN MODEL SECTION
              $epochs = $_POST["epochs"];
              $batch_size = $_POST["batch_size"];
              $validation_split = $_POST["validation_split"];
              echo "<hr> <pre class='pre-title'> \tStep 4: -- TRAIN MODEL -- </pre>";
              $exit = trainModel($dataset, $epochs, $batch_size, $validation_split, $output_classes);
              if($exit != 0) $errors = "True";

              //EVALUATE MODEL SECTION
              $evaluate_choose = $_POST["evaluate_choose"];
              if($evaluate_choose == "true"){
                echo "<hr> <pre class='pre-title'> \tStep 5: -- EVALUATE MODEL -- </pre>";
                $exit = evaluateModel($dataset, $output_classes, $metrics);
                if($exit != 0) $errors = "True";
              }

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
      <?php
        if($errors == "False"){
          echo "<div class='download-model'>
                  <a href='python/saves/personal_model.h5'>
                    <div class='cloud-download-container'><img src='assets/img/cloud-download.png' alt='download-img'></div>
                    Download your trained model!
                  </a>
                </div>";
        }
      ?>
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
  /* --- SAVE DATASET --- */
  function saveDataset($dataset_tmp_name, $dataset_name){
    $type = "";
    if(strstr(substr($dataset_name, -5), ".csv")) $type = "csv";
    elseif(strstr(substr($dataset_name, -6), ".json")) $type = "json";
    elseif(strstr(substr($dataset_name, -5), ".pkl")) $type = "pkl";
    $dataset_original_name = $dataset_name;
    $dataset_name = "local_dataset.$type";

    $local_path = "./python/saves/" . $dataset_name;
    if(move_uploaded_file($dataset_tmp_name, $local_path)){
      echo "<pre class='pre-content'>Dataset $dataset_original_name saved!</pre>";
      return $dataset_name;
    }else{
        echo "Failed to upload .";
        return -1;
    }
  }

  /* --- BUILD MODEL --- */
  function buildModel($model_type, $layers_number, $output_classes, $input_shape, $layers){
    global $python_path;
    $filename = saveLayers($layers);

    if($filename !== -1){
      $cmd = escapeshellcmd("$python_path ./python/build_model.py $model_type $layers_number $filename $input_shape");

      echo("\n model_type: $model_type \n layers_number: $layers_number \n input_shape: $input_shape");
      $exit_status = exec_script($cmd);
      if($exit_status != 0){
        echo "<br>ERROR BUILDING THE MODEL ...";
      }
    }else{
      echo "<br>ERROR. Failed to save layers configuration.";
      return $filename;   // -1
    }
    return $exit_status;
  }

  function saveLayers($layers){
    $json_config = json_encode($layers);
    $filepath = "./python/saves/";
    $filename = $filepath . "layers_config.json";

    if($myfile = fopen($filename, "w")){
      fwrite($myfile, $json_config);
      fclose($myfile);
      return $filename;
    }else{
      return -1;
    }
  }

  /* --- COMPILE MODEL --- */
  function compileModel($optimizer, $learning_rate, $output_classes, $metrics){
    global $python_path;
    $cmd = escapeshellcmd("$python_path ./python/compile_model.py $optimizer $learning_rate $output_classes");

    echo("\n optimizer: $optimizer \n learning_rate: $learning_rate");
    $exit_status = exec_script($cmd);
    if($exit_status != 0)
      echo "<br>ERROR COMPILING THE MODEL ...";

    return $exit_status;
  }

  /* --- TRAIN MODEL --- */
  function trainModel($dataset, $epochs, $batch_size, $validation_split, $output_classes){
    global $python_path;
    $cmd = escapeshellcmd("$python_path ./python/train_model.py $dataset $epochs $batch_size $validation_split $output_classes");

    echo("\n epochs: $epochs \n batch_size: $batch_size \n validation_split: $validation_split \n output_classes: $output_classes");
    $exit_status = exec_script($cmd);
    if($exit_status != 0)
      echo "<br>ERROR TRAINING THE MODEL ...";

    return $exit_status;
  }

  /* --- EVALUATE MODEL --- */
  function evaluateModel($dataset, $output_classes, $metrics){
    global $python_path;
    $cmd = escapeshellcmd("$python_path ./python/evaluate_model.py $dataset $output_classes");

    $exit_status = exec_script($cmd);
    if($exit_status != 0)
      echo "<br>ERROR EVALUATING THE MODEL ...";

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
