<?php


namespace App;

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
}