<?php

namespace App;

use App\Dataset;
use App\Network;
use App\User;

use Exception;
// use Carbon\Carbon;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class Training extends Model
{
    protected $fillable = [
        'user_id', 'model_id', 'dataset_id_training', 'dataset_id_test', 'is_evaluated', 'train_description', 'epochs', 'batch_size', 'validation_split',
        'checkpoint_filepath', 'save_best_only', 'filepath_epochs_log',
    ];

    // Start training function
    public function startTraining(Network $model, Dataset $dataset_training){
        try {
            // Exec script	
            $app_path = base_path();
            $data_train_path = $dataset_training->local_path;
            $epochs = $this->epochs;
            $batch_size = $this->batch_size;
            $valid_split = $this->validation_split;
            $output_classes = $model->output_classes;

            $checkpoint_path = $this->checkpoint_filepath;
            $save_best = $this->save_best_only;
            $epochs_log_path = $this->filepath_epochs_log;

            /*
            $process = new Process("python3 $app_path/resources/python/train_model.py \"$data_train_path\" $epochs $batch_size $valid_split $output_classes \"$checkpoint_path\" $save_best \"$epochs_log_path\"");
            $process->mustRun();
            echo($process->getOutput());
            //base_path();
            */

        } catch (\Throwable $th) {
            throw $th;
        }
    } 
}