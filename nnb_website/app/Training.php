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

            $checkpoint_path = $this->checkpoint_filepath."model_".$model->id.".h5";
            $save_best = $this->save_best_only;
            $epochs_log_path = $this->filepath_epochs_log;

            // Get model_size before training the model
            $model_size_before = Storage::size("public/$model_path");

            $process = new Process("python3 $app_path/resources/python/train_model.py \"$app_path\" \"$data_train_path\" \"$model_path\" $epochs $batch_size $valid_split $output_classes \"$checkpoint_path\" $save_best \"$epochs_log_path\"");
            $process->setTimeout(86400);    // 24 hours
            $process->setIdleTimeout(600);  // 10 mins (time since the last output)
            $process->mustRun(
                function ($type, $buffer) use ($user, $model) {
                    if (Process::ERR === $type) {
                        echo '--- ERR --- > '.$buffer;
                    } else {
                        // Print user and training info
                        echo(PHP_EOL."User $user->id: $user->username | Training_id: $this->id".PHP_EOL);   // PHP_EOL = \n

                        // Get epoch
                        $epochs_info = $this->getEpochInfo($buffer);
                        
                        // epochs info
                        if(isset($epochs_info[0])){
                            $current_epoch = $epochs_info[0]+1;
                            $this->training_percentage = round($current_epoch/$this->epochs, 2);
                            $this->update();
                            print_r("Epochs: $current_epoch/$this->epochs".PHP_EOL);
                        }

                        // accuracy info
                        if(isset($epochs_info[1])){
                            $current_accuracy = $epochs_info[1];
                            $model->accuracy = round($current_accuracy, 2);
                            $model->update();
                            print_r("Accuracy: $current_accuracy".PHP_EOL);
                        }

                        // loss info
                        if(isset($epochs_info[2])){
                            $current_loss = $epochs_info[2];
                            $model->loss = round($current_loss, 2);
                            $model->update();
                            print_r("Loss: $current_loss".PHP_EOL);
                        }

                        // validation info
                        if($this->valid_split){
                            // val_accuracy
                            if(isset($epochs_info[3])){
                                $current_val_accuracy = $epochs_info[3];
                                $model->accuracy = round($current_val_accuracy, 2);
                                $model->update();
                                print_r("Val_accuracy: $current_val_accuracy".PHP_EOL);
                            }

                            // val_loss
                            if(isset($epochs_info[4])){
                                $current_val_loss = $epochs_info[4];
                                $model->loss = round($current_val_loss, 2);
                                $model->update();
                                print_r("Val_loss: $current_val_loss".PHP_EOL);
                            }
                        }

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
        }
    }

    private function getEpochInfo(string $buffer){
        $epochs_info = array();
        
        if (($handle = fopen(storage_path()."/app/".$this->filepath_epochs_log, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE)
                $epochs_info = $data;
            fclose($handle);
        }

        return $epochs_info;

        /*
        $buffer_epoch = strstr($buffer, "Epoch");
        $buffer_loss = strstr($buffer, "loss");
        $buffer_acc = strstr($buffer, "accuracy");
        $buffer_epoch ? $buffer_epoch : "";
        $buffer_loss ? $buffer_loss : "";
        $buffer_acc ? $buffer_acc : "";

        //       ====>     0 1 2 3 4 5 6 7 8                           
        //       ====>     E p o c h _ 9 0 / 1 0 0
        // get Epoch 
        $start_pos = strpos($buffer_epoch, "Epoch");    // 0
        if($start_pos !== FALSE){
            $start_pos += strlen("Epoch")+1;            // 0+6 = 6
            $end_pos = strpos($buffer_epoch, "/");      // 8
            if($end_pos !== FALSE){
                $chars_len = $end_pos - $start_pos;     // 8 - 6 = 2
                $epoch_val = (int)(substr($buffer_epoch, $start_pos, $chars_len));
                $epochs_info[0] = $epoch_val;
            }
        }

        //       ====>     0 1 2 3 4 5 6 7 8 9 10                         
        //       ====>     l o s s : _ 0 . 5 _ -
        // get Loss 
        $start_pos = strpos($buffer_loss, "loss");      // 0
        if($start_pos !== FALSE){
            $start_pos += strlen("loss:")+1;            // 0+6 = 6
            $end_pos = strpos($buffer_loss, "-");       // 10
            if($end_pos !== FALSE){
                $chars_len = $end_pos-1 - $start_pos;   // 9 - 6 = 3
                $loss_val = (float)(substr($buffer_loss, $start_pos, $chars_len));
                $epochs_info[1] = $loss_val;
            }
        }

        //       ====>     0 1 2 3 4 5 6 7 8 9 10 11 12 13                   
        //       ====>     a c c u r a c y : _ 0  .  5  \0 (EOF)
        // get Accuracy 
        $start_pos = strpos($buffer_acc, "accuracy");   // 0
        if($start_pos !== FALSE){
            $start_pos += strlen("accuracy:")+1;        // 0+10 = 10
            $end_pos = strlen($buffer_acc);             // 13
            
            $chars_len = $end_pos - $start_pos;         // 13 - 10 = 3
            $acc_val = (float)(substr($buffer_acc, $start_pos, $chars_len));
            $epochs_info[2] = $acc_val;
        }

        return $epochs_info;
        */
    }
}