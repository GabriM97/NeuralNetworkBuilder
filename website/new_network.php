<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>NNB - Build Model</title>
  </head>
  <body>


    <?php
      $cmd = escapeshellcmd("C:/Users/gabry/AppData/Local/Programs/Python/Python37/python.exe ./python/builder.py");
      //$exit_status = exec_script($cmd);
      //echo "<hr>exit: $exit_status";

      saveData();

    ?>

  </body>
</html>


<?php

  function saveData(){
    // DATASET SECTION
    $dataset_tmp_name = $_FILES["import_dataset"]["tmp_name"];
    $dataset_name = $_FILES["import_dataset"]["name"];
    saveDataset($dataset_tmp_name, $dataset_name);

    // BUILD MODEL SECTION
    $model_type = $_POST["model_type"];
    $layers_number= $_POST["layers_number"];
    $output_classes = $_POST["output_classes"];   // == $layer['neurons_number'][-1]

    // LAYERS SECTION
    $layers = array();
    for($i = 0; $i < $layers_number; $i++){
      $layers[] = array("neurons_number" => $_POST["neur_number"][$i],
                        "activ_function" => $_POST["activ_funct"][$i]);
    }
    foreach ($layers as $layer){
      echo "<br>Neurons: " . $layer['neurons_number'] . " | Activ_Function: " . $layer['activ_function'];
    }

    // COMPILE MODEL SECTION
    $learning_rate = $_POST["learning_rate"];
    $optimizer = $_POST["optimizer"];
    $metrics = $_POST["metrics"];

    // TRAIN MODEL SECTION
    $ephocs = $_POST["epochs"];
    $batch_size = $_POST["batch_size"];
    $validation_split = $_POST["validation_split"];

    //EVALUATE MODEL SECTION
    $evaluate_choose = $_POST["evaluate_choose"];
  }

  function saveDataset($dataset_tmp_name, $dataset_name){
    $type = "NULL";
    if(strstr(substr($dataset_name, -4), ".csv")) $type = "csv";
    if(strstr(substr($dataset_name, -6), ".json")) $type = "json";
    $dataset_name = "local_dataset.$type";

    if(move_uploaded_file($dataset_tmp_name, "./python/" . $dataset_name)){
        return 0;
    }else{
        echo "Failed to upload.";
        return -1;
    }

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
