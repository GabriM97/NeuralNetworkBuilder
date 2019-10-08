<?php


namespace App;

use Illuminate\Database\Eloquent\Model;

//$python_path = "C:/Users/Gabriele/AppData/Local/Programs/Python/Python37/python.exe";  //windows
$python_path = "/usr/bin/python";  //linux

class Network extends Model
{
    protected $fillable = ['user_id', 'model_type', 'input_shape', 'layers_num', 'output_classes', 'model_name', 'model_description', 'file_size', 'local_path'];

    /* --- BUILD MODEL --- */
    public function build_h5_model($user_id, $model_id, $local_path, $model_type, $layers_number, $output_classes, $input_shape, $neurons_number, $activ_function){

        //throw new Exception('exception description');
    
        global $python_path;
        $filename = saveLayers($user_id, $model_id, $neurons_number, $activ_function);

        if($filename !== -1){
            $cmd = escapeshellcmd("$python_path /resources/python/build_model.py $model_type $layers_number $filename $input_shape");

            //echo("\n model_type: $model_type \n layers_number: $layers_number \n input_shape: $input_shape");
            $exit_status = exec_script($cmd);
            if($exit_status != 0){
                throw new Exception('ERROR BUILDING THE MODEL');
            }
        }else{
            throw new Exception('ERROR. Failed to save layers configuration');
        }
        return $exit_status;
    }

    function saveLayers($user_id, $model_id, $neurons_number, $activ_function){
        $layers = array("neurons_number" => $neurons_number,
                        "activ_function" => $activ_function);
        $json_config = json_encode($layers);

        $hashed_user = hash("md5", $user_id);
        $filepath = "users/$hashed_user/layers_config/";
        $filename = $filepath . "model_$model_id"."_layers_config.json";

        if($myfile = fopen($filename, "w")){
            fwrite($myfile, $json_config);
            fclose($myfile);
            return $filename;
        }else{
            return -1;
        }
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

}
