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
    public function startTraining(User $user, Network $model, Dataset $dataset_training){
        try {

            // Get parameters
            $app_path = base_path();
            $data_train_path = $dataset_training->local_path;
            $model_path = $model->local_path;
            $epochs = $this->epochs;
            $batch_size = $this->batch_size;
            $valid_split = $this->validation_split;
            $output_classes = $model->output_classes;

            $checkpoint_path = $this->checkpoint_filepath;
            $save_best = $this->save_best_only;
            $epochs_log_path = $this->filepath_epochs_log;

            // Get model_size before training the model
            $model_size_before = Storage::size("public/$model_path");

            $process = new Process("python3 $app_path/resources/python/train_model.py \"$app_path\" \"$data_train_path\" \"$model_path\" $epochs $batch_size $valid_split $output_classes \"$checkpoint_path\" $save_best \"$epochs_log_path\"");
            $process->mustRun(
                function ($type, $buffer) {
                    if (Process::ERR === $type) {
                        echo '--- ERR --- > '.$buffer;
                    } else {
                        echo $buffer;
                    }
                }
            );

            $model->is_trained = true;

            // TEMPORARY VALUES
            $this->training_percentage = 1;
            $model->accuracy = 0.9;
            $model->loss = 0.3;
            
            // Update user->available_space with new model_size
            $model_size_after = Storage::size("public/$model_path");
            $model->file_size = $model_size_after;

            $size_diff = $model_size_after - $model_size_before;
            if($user->available_space < $size_diff)
                $user->available_space = 0;
            else 
                $user->available_space -= $size_diff;

            $user->update();
            $model->update();
            $this->update();

        } catch (ProcessFailedException $err) {
            throw new Exception($process->getErrorOutput());
        }
    } 
}