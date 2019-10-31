<?php


namespace App;

use App\Training;
use App\Compilation;
use App\Dataset;
use App\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Exception;

class Network extends Model
{
	protected $fillable = ['user_id', 'model_type', 'input_shape', 'layers_number', 'output_classes', 'model_name', 'model_description', 'file_size', 'local_path'];

    /* --- BUILD MODEL --- */				
    public static function build_h5_model($model_id, $local_dir, $model_type, $layers_number, $input_shape, $neurons_number, $activ_function){
											// $local_dir = users/$hashed_user/models/
		try {
			// Save layers config
			$layers_filepath = Network::saveLayers($model_id, $local_dir, $neurons_number, $activ_function);
			
			// Exec script	
			$app_path = base_path();																									
			$process = new Process("python3 $app_path/resources/python/build_model.py $model_id $model_type $layers_number \"$local_dir\" $input_shape");
			$process->mustRun();

			Storage::setVisibility("public/$local_dir/model_$model_id.h5", 'public');
			Storage::delete($layers_filepath);

			$model_size = Storage::size("public/$local_dir/model_$model_id.h5");
			return $model_size;

		} catch (\Throwable $th) {
			// Catch errors
			Storage::delete($layers_filepath);
			Storage::delete("public/$local_dir/model_$model_id.h5");
			throw $th;
		}
    }

	// SAVE LAYERS FUNCTION
    public static function saveLayers($model_id, $local_dir, $neurons_number, $activ_function){
		// Build json file
        $layers = array("neurons_number" => $neurons_number,
						"activ_function" => $activ_function);
        $json_config = json_encode($layers);
		
		try {
			// Save file
			$filename = $local_dir . "model_$model_id"."_layers_config.json";
			Storage::put($filename, $json_config, 'private');
			
			return $filename;

		} catch (\Throwable $th) {
			throw $th;
		}
	}
	
	// Evaluate the model
	public function evaluateModel(User $user, Training $training, Dataset $dataset_test){
		
		echo(PHP_EOL."EVALUATION - [Training_id: $training->id - User $user->id: $user->username]".PHP_EOL);
		echo("--- Starting Evaluation ---".PHP_EOL);

		$training->return_message = "Evaluating the model...";
		$training->update();
		
		// Get parameters
		$app_path = base_path();
		$data_test_path = $dataset_test->local_path;
		$model_path = $this->local_path;
		$batch_size = $training->batch_size;
		$log_path = $training->filepath_epochs_log;
		$metrics_list = Compilation::where("model_id", $this->id)->first()->metrics;
		$output_classes = $this->output_classes;
		
		try {
			$process = new Process("python3 $app_path/resources/python/evaluate_model.py \"$app_path\" \"$data_test_path\" $batch_size \"$model_path\" $output_classes \"$log_path\"");
			$process->setTimeout(86400);    // 24 hours
            $process->setIdleTimeout(600);
			$process->mustRun(
				function ($type, $buffer) use ($user, $training, $process) {
					$process_pid = $process->getPid();
					$training->process_pid = $process_pid;
					$training->update();

					// Print pid, training and user info
					echo(PHP_EOL."EVALUATION - [PID: $process_pid - Training_id: $training->id - User $user->id: $user->username]".PHP_EOL);   // PHP_EOL = \n
					$evaluation_info = $this->getEvaluationInfo($buffer, $training);
					$training->return_message = "Evaluating the model...";

					// accuracy info
					if(isset($evaluation_info[2])){
						$current_accuracy = $evaluation_info[2];
						$this->accuracy = round($current_accuracy, 2);
						print_r("Eval_accuracy: $current_accuracy".PHP_EOL);
						$training->return_message .= " ---> Accuracy: ".($this->accuracy*100)."%";
					}

					// loss info
					if(isset($evaluation_info[3])){
						$current_loss = $evaluation_info[3];
						$this->loss = round($current_loss, 2);
						print_r("Eval_loss: $current_loss".PHP_EOL);
						$training->return_message .= " | Loss: ".($this->loss*100)."% ";
					}

					$this->update();
					$training->update();
				}
			);

		} catch (ProcessFailedException $err) {
            throw new Exception($process->getErrorOutput());

        } catch (ProcessSignaledException $err){
            if($process->getTermSignal() == SIGKILL)
            	throw new Exception("Evaluation stopped. You cannot resume the evaluation.", self::THROW_STOPPROCESS);
            else
                throw new Exception($process->getErrorOutput());
        } catch (\Throwable $th){
            throw $th;
        }
	}

	// Get evaluation info from log file
    private function getEvaluationInfo(string $buffer, $training){
        $evaluation_info = array();
        
        if (($handle = fopen(storage_path()."/app/".$training->filepath_epochs_log, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE)
                $evaluation_info = $data;
            fclose($handle);
        }else{
			print("File log not found");
		}

        return $evaluation_info;
	}
}