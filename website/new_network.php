<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>NNB - Build Model</title>
  </head>
  <body>


    <?php

      saveData();

    ?>

  </body>
</html>


<?php

  function saveData(){
    // DATASET SECTION
    $dataset_tmp_name = $_FILES["import_dataset"]["tmp_name"];
    $dataset_name = $_FILES["import_dataset"]["name"];
    $input_shape = $_POST["input_shape"];
    $dataset = saveDataset($dataset_tmp_name, $dataset_name);
    echo "<hr>";

    // BUILD MODEL SECTION
    $model_type = $_POST["model_type"];
    $layers_number= $_POST["layers_number"];
    $output_classes = $_POST["output_classes"];   // == $layer['neurons_number'][-1]

        // LAYERS SECTION
    $layers = array("neurons_number" => array(), "activ_function" => array());
    for($i = 0; $i < $layers_number; $i++){
      $layers["neurons_number"][$i] = $_POST["neur_number"][$i];
      $layers["activ_function"][$i] = $_POST["activ_funct"][$i];
    }
    buildModel($model_type, $layers_number, $output_classes, $input_shape, $layers);
    echo "<hr>";

    // COMPILE MODEL SECTION
    $learning_rate = $_POST["learning_rate"];
    $optimizer = $_POST["optimizer"];
    $metrics = $_POST["metrics"];
    compileModel($optimizer, $learning_rate, $output_classes, $metrics);
    echo "<hr>";

    // TRAIN MODEL SECTION
    $epochs = $_POST["epochs"];
    $batch_size = $_POST["batch_size"];
    $validation_split = $_POST["validation_split"];
    trainModel($dataset, $epochs, $batch_size, $validation_split, $output_classes);
    echo "<hr>";

    //EVALUATE MODEL SECTION
    $evaluate_choose = $_POST["evaluate_choose"];
    if($evaluate_choose == "true"){
      evaluateModel($dataset, $output_classes, $metrics);
      echo "<hr>";
    }
  }

  /* --- SAVE DATASET --- */
  function saveDataset($dataset_tmp_name, $dataset_name){
    $type = "";
    if(strstr(substr($dataset_name, -5), ".csv")) $type = "csv";
    elseif(strstr(substr($dataset_name, -6), ".json")) $type = "json";
    elseif(strstr(substr($dataset_name, -5), ".pkl")) $type = "pkl";
    $dataset_name = "local_dataset.$type";

    $local_path = "./python/saves/" . $dataset_name;
    if(move_uploaded_file($dataset_tmp_name, $local_path)){
      echo "<br>Dataset saved.";
      return $dataset_name;
    }else{
        echo "<br>Failed to upload.";
        return -1;
    }

  }

  /* --- BUILD MODEL --- */
  function buildModel($model_type, $layers_number, $output_classes, $input_shape, $layers){
    $filename = saveLayers($layers);

    if($filename !== -1){
      $cmd = escapeshellcmd("C:/Users/gabry/AppData/Local/Programs/Python/Python37/python.exe " .
                            "./python/build_model.py $model_type $layers_number $filename $input_shape");
      $exit_status = exec_script($cmd);
      if($exit_status != 0)
        echo "<br>ERROR BUILDING THE MODEL ...";
      else
        echo "<br>Model Builded!";
    }else{
      echo "<br>ERROR. Failed to save layers configuration.";
    }

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
    $cmd = escapeshellcmd("C:/Users/gabry/AppData/Local/Programs/Python/Python37/python.exe " .
                          "./python/compile_model.py $optimizer $learning_rate $output_classes");
    $exit_status = exec_script($cmd);
    if($exit_status != 0)
      echo "<br>ERROR COMPILING THE MODEL ...";
    else
      echo "<br>Model Compiled!";
  }

  /* --- TRAIN MODEL --- */
  function trainModel($dataset, $epochs, $batch_size, $validation_split, $output_classes){
    $cmd = escapeshellcmd("C:/Users/gabry/AppData/Local/Programs/Python/Python37/python.exe " .
                          "./python/train_model.py $dataset $epochs $batch_size $validation_split $output_classes");
    $exit_status = exec_script($cmd);
    if($exit_status != 0)
      echo "<br>ERROR TRAINING THE MODEL ...";
    else
      echo "<br>Model Trained!";
  }

  /* --- EVALUATE MODEL --- */
  function evaluateModel($dataset, $output_classes, $metrics){
    $cmd = escapeshellcmd("C:/Users/gabry/AppData/Local/Programs/Python/Python37/python.exe " .
                          "./python/evaluate_model.py $dataset $output_classes");
    $exit_status = exec_script($cmd);
    if($exit_status != 0)
      echo "<br>ERROR EVALUATING THE MODEL ...";
    else
      echo "<br>Model evaluated!";
  }


  function exec_script($cmd){
    while (@ ob_end_flush());   // end all output buffers if any
    $proc = popen($cmd, 'r');

    $live_output = "";
    $all_output = "";
    echo "<pre>";

    while(!feof($proc)){
        $live_output = fread($proc, 1);     //read each 5 Byte (5 char)
        $all_output = $all_output . $live_output;
        echo $live_output;
        @ flush();
    }

    echo "</pre>";
    pclose($proc);

    if($exit_status = strstr($all_output, "exit_status"))
      return substr($exit_status, -2, -1);
    else
      return -1;
  }

  function test_cmd(){
    $command = escapeshellcmd("C:/Users/gabry/AppData/Local/Programs/Python/Python37/python.exe ./python/builder.py");
    system($command);
  }
?>
