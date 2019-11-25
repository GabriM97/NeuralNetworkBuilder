<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Exception;

class Compilation extends Model
{
    protected $fillable = ['model_id', 'learning_rate', 'optimizer', 'metrics'];
    
    public static function compileModel(Network $model, string $optimizer, float $learning_rate, string $metrics_list=NULL){
        try {
            // Exec script
            $model_path = $model->local_path;
            $output_classes = $model->output_classes;
			$app_path = base_path();																									
            $process = new Process("python3 $app_path/resources/python/compile_model.py \"$model_path\" $optimizer $learning_rate $output_classes $metrics_list");
            $process->mustRun();
            
            $model_size = Storage::size("public/$model_path");
			return $model_size;

		} catch (\Throwable $th) {
			// Catch errors
			throw $th;
        }
    }
}
