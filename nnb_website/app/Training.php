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
use Symfony\Component\Process\Exception\ProcessSignaledException;
use Symfony\Component\Process\Process;

class Training extends Model
{
    protected $fillable = [
        'user_id', 'model_id', 'dataset_id_training', 'dataset_id_test', 'is_evaluated', 'train_description', 'epochs', 'batch_size', 'validation_split',
        'checkpoint_filepath', 'save_best_only', 'filepath_epochs_log',
    ];

    const THROW_SETPAUSE = 3;
    const THROW_STOPPROCESS = 4;

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

            $checkpoint_path = $this->checkpoint_filepath."model_".$model->id.".h5";
            $save_best = $this->save_best_only;
            $epochs_log_path = $this->filepath_epochs_log;

            // Get model_size before training the model
            $model_size_before = Storage::size("public/$model_path");

            $process = new Process("python3 $app_path/resources/python/train_model.py \"$app_path\" \"$data_train_path\" \"$model_path\" $epochs $batch_size $valid_split $output_classes \"$checkpoint_path\" $save_best \"$epochs_log_path\"");
            $process->setTimeout(86400);    // 24 hours
            $process->setIdleTimeout(600);  // 10 mins (time since the last output)
            $process->mustRun(
                function ($type, $buffer) use ($user, $model, $process) {
                    if (Process::ERR === $type) {
                        echo '--- ERR --- > '.$buffer;
                    } else {
                        // Print pid, training and user info
                        $process_pid = $process->getPid();
                        $this->process_pid = $process_pid;
                        echo(PHP_EOL."PID: $process_pid - Training_id: $this->id | User $user->id: $user->username".PHP_EOL);   // PHP_EOL = \n

                        // Get epoch
                        $epochs_info = $this->getEpochInfo($buffer);
                        
                        // epochs info
                        if(isset($epochs_info[0])){
                            $current_epoch = $epochs_info[0]+1;
                            $this->training_percentage = round($current_epoch/$this->epochs, 2);
                            print_r("Epochs: $current_epoch/$this->epochs".PHP_EOL);
                        }

                        // accuracy info
                        if(isset($epochs_info[1])){
                            $current_accuracy = $epochs_info[1];
                            $model->accuracy = round($current_accuracy, 2);
                            print_r("Accuracy: $current_accuracy".PHP_EOL);
                        }

                        // loss info
                        if(isset($epochs_info[2])){
                            $current_loss = $epochs_info[2];
                            $model->loss = round($current_loss, 2);
                            print_r("Loss: $current_loss".PHP_EOL);
                        }

                        // validation info
                        if($this->validation_split){
                            // val_accuracy
                            if(isset($epochs_info[3])){
                                $current_val_accuracy = $epochs_info[3];
                                $model->accuracy = round($current_val_accuracy, 2);
                                print_r("Val_accuracy: $current_val_accuracy".PHP_EOL);
                            }

                            // val_loss
                            if(isset($epochs_info[4])){
                                $current_val_loss = $epochs_info[4];
                                $model->loss = round($current_val_loss, 2);
                                print_r("Val_loss: $current_val_loss".PHP_EOL);
                            }
                        }
                        $this->update();
                        $model->update();
                    }
                }
            );
            
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

        } catch (ProcessFailedException $err) {
            throw new Exception($process->getErrorOutput());

        } catch (ProcessSignaledException $err){
            if($process->getTermSignal() == SIGTERM)
                throw new Exception("Training paused. Click 'Resume' button to resume your training from the last saved model.", self::THROW_SETPAUSE);
            else 
                if($process->getTermSignal() == SIGKILL)
                    throw new Exception("Training stopped. You cannot resume the training.", self::THROW_STOPPROCESS);
                else
                    throw new Exception($process->getErrorOutput());
        }
    }

    // Get Epoch Info from log file
    private function getEpochInfo(string $buffer){
        $epochs_info = array();
        
        if (($handle = fopen(storage_path()."/app/".$this->filepath_epochs_log, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE)
                $epochs_info = $data;
            fclose($handle);
        }

        return $epochs_info;
    }
}