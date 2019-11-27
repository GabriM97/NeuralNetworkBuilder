<?php

namespace App;

use App\Dataset;
use App\Network;
use App\Compilation;
use App\User;
use App\Node;

use Exception;
use GuzzleHttp\Client;
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

    const THROW_DEFAULT = 0;
    const THROW_SETPAUSE = 3;
    const THROW_STOPPROCESS = 4;
    const THROW_CANTRUN = 5;

    // Start training function
    public function startTraining(User $user, Network $model, Dataset $dataset_training){
        $old_status=$this->status;

        echo(PHP_EOL."[Training_id: $this->id - User $user->id: $user->username]".PHP_EOL);   // PHP_EOL = \n
        if($old_status == "stopped"){
            echo("--- Starting new training ---".PHP_EOL);
        }else{ //$old_status == "paused"
            echo("--- Resuming training ---".PHP_EOL);
        }

        // Get parameters
        $app_path = base_path();
        $data_train_path = $dataset_training->local_path;
        $model_path = $model->local_path;
        $diff_epochs = $this->epochs - $this->executed_epochs;   // if it's first start, executed_epochs = 0
        $exec_epochs = $this->executed_epochs;
        $batch_size = $this->batch_size;
        $valid_split = $this->validation_split;
        $output_classes = $model->output_classes;

        $checkpoint_path = $this->checkpoint_filepath."model_".$model->id.".h5";
        $save_best = $this->save_best_only;
        $epochs_log_path = $this->filepath_epochs_log;

        // Get model_size before training the model
        $model_size_before = Storage::size("public/$model_path");

        // Set index
        $compile = Compilation::where("model_id", $model->id)->first();
        $metrics_list = $compile->metrics;

        $epoch_index = 0;
        if($metrics_list){
            $acc_index = 1;
            $loss_index = 2;
            $val_acc_index = 3;
            $val_loss_index = 4;
        }else{
            $acc_index = -1;
            $loss_index = 1;
            $val_acc_index = -1;
            $val_loss_index = 2;
        }

        try {
            $script_name = "$app_path/resources/python/train_model.py";
            $options = "\"$data_train_path\" \"$model_path\" $diff_epochs $batch_size $valid_split $output_classes \"$checkpoint_path\" $save_best \"$epochs_log_path\"";
            
            $node = Node::getLightestNode();
            $ip_addr = $node->ip_address;
            if(!$node->is_webserver){
                $client = new Client();
                $result = $client->request('POST', "http://$ip_addr:5050/start", [
                    'form_params' => [
                        'training_id' => $this->id,
                        'script' => 'train_model.py',
                        'options' => " \".\" $options false",  //false, it isn't webserver
                    ]
                ]);
                $res = $result->getBody();
                if(strpos(strtolower($res), "error") !== false)
                    throw new Exception("Error running training on node $ip_addr: $res", self::THROW_CANTRUN);
                
                $pid = $res;
                $this->process_pid = $pid;
                $this->status = "started";
                $this->return_message = "Training in progress...";
                $this->processing_node_id = $node->id;
                $this->update();
                $node->running_trainings++;
                $node->update();
                
                $handle = fopen(storage_path()."/app/".$this->filepath_epochs_log, "w");
                fclose($handle);

                while($this->manageTrainingInfo($user, $model, $pid, $exec_epochs, 
                                                $epoch_index, $acc_index, $loss_index, 
                                                $val_acc_index, $val_loss_index, $ip_addr) < 1){
                    sleep(1);
                    
                    $signal = $this->training_node_signal;
                    // Stop the process
                    if($signal == "stopprocess"){
                        $this->training_node_signal = NULL;
                        $this->update();
                        throw new Exception("Training stopped. You cannot resume the training.", self::THROW_STOPPROCESS);
                    }elseif($signal == "setpause"){     // "Pause" the process
                        $this->training_node_signal = NULL;
                        $this->update();
                        throw new Exception("Training paused. Click 'Resume' button to resume your training from the last saved model.", self::THROW_SETPAUSE);
                    }else{
                        //Check process status
                        $client = new Client();
                        $result = $client->request('GET', "http://$ip_addr:5050/checkStatus", [
                            'query' => [
                                "process_pid=$this->process_pid",
                                "training_id=$this->id",
                                ]
                        ]);
                        $res = $result->getBody();
                        if(strpos(strtolower($res), "error") !== false)
                            throw new Exception("Error checking training status on node $ip_addr: $res");
                        else
                            if((strpos(strtolower($res), "stop") !== false) || (strpos(strtolower($res), "kill") !== false))  // process does not exists or is not running
                                if($this->training_node_signal === NULL)
                                    break;  //exit the while loop
                    }
                }
                $node->running_trainings--;
                $node->update();
            }else{
                // --- START Training Process ---
                $process = new Process("python3 $script_name \"$app_path\" $options true"); //true, it's webserver
                $process->setTimeout(86400);    // 24 hours
                $process->setIdleTimeout(600);  // 10 mins (time since the last output)
                
                $this->processing_node_id = $node->id;
                $this->update();
                $node->running_trainings++;
                $node->update();

                $process->mustRun(
                    function ($type, $buffer) use ($user, $model, $process, $exec_epochs, $old_status, $epoch_index, $acc_index, $loss_index, $val_acc_index, $val_loss_index, $ip_addr) {
                        if (Process::ERR === $type) {
                            //echo '--- ERR --- > '.$buffer;
                        } else {
                            if($old_status == "stopped" || $old_status == "paused"){
                                $process_pid = $process->getPid();
                                $this->process_pid = $process_pid;
                                $this->status = "started";
                                $this->return_message = "Training in progress...";
                                $this->update();
                            }
                            $this->manageTrainingInfo($user, $model, $process_pid, $exec_epochs, 
                                                      $epoch_index, $acc_index, $loss_index, 
                                                      $val_acc_index, $val_loss_index, $ip_addr);
                        }
                    }
                );
                // --- STOP Training Process ---
                $node->running_trainings--;
                $node->update();
            }

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
        } catch (Exception $err){
            if($err->getCode() == self::THROW_CANTRUN){
                $node->running_trainings++;     //it will be decremented in TrainingJob on_fail() method
                $node->update();
                throw new Exception($err->getMessage());
            }else
                if($err->getCode() == self::THROW_SETPAUSE)
                    throw new Exception("Training paused. Click 'Resume' button to resume your training from the last saved model.", self::THROW_SETPAUSE);
                else
                    if($err->getCode() == self::THROW_STOPPROCESS)
                        throw new Exception("Training stopped. You cannot resume the training.", self::THROW_STOPPROCESS);
                    else
                        throw new Exception($err->getMessage());
        }
    }

    // Get Epoch Info from log file
    private function getEpochInfo(){
        $epochs_info = array();
        
        if (($handle = fopen(storage_path()."/app/".$this->filepath_epochs_log, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE)
                $epochs_info = $data;
            fclose($handle);
        }

        return $epochs_info;
    }

    private function manageTrainingInfo($user, $model, $process_pid, $exec_epochs, $epoch_index, $acc_index, $loss_index, $val_acc_index, $val_loss_index, $ip_addr){
        echo(PHP_EOL."[Node: $ip_addr - PID: $process_pid - Training_id: $this->id - User $user->id: $user->username]".PHP_EOL);   // PHP_EOL = \n

        // Get epoch
        $epochs_info = $this->getEpochInfo();
        
        // epochs info
        if(isset($epochs_info[$epoch_index])){
            $current_epoch = $epochs_info[$epoch_index]+1 + $exec_epochs;
            $this->executed_epochs = $current_epoch;
            $this->training_percentage = round($current_epoch/$this->epochs, 2);
            print_r("Epochs: $current_epoch/$this->epochs".PHP_EOL);
        }

        // accuracy info
        if(isset($epochs_info[$acc_index])){
            $current_accuracy = $epochs_info[$acc_index];
            $model->accuracy = round($current_accuracy, 2);
            print_r("Accuracy: $current_accuracy".PHP_EOL);
        }

        // loss info
        if(isset($epochs_info[$loss_index])){
            $current_loss = $epochs_info[$loss_index];
            $model->loss = round($current_loss, 2);
            print_r("Loss: $current_loss".PHP_EOL);
        }

        // validation info
        if($this->validation_split){
            // val_accuracy
            if(isset($epochs_info[$val_acc_index])){
                $current_val_accuracy = $epochs_info[$val_acc_index];
                $model->accuracy = round($current_val_accuracy, 2);
                print_r("Val_accuracy: $current_val_accuracy".PHP_EOL);
            }

            // val_loss
            if(isset($epochs_info[$val_loss_index])){
                $current_val_loss = $epochs_info[$val_loss_index];
                $model->loss = round($current_val_loss, 2);
                print_r("Val_loss: $current_val_loss".PHP_EOL);
            }
        }
        $this->update();
        $model->update();

        return $this->training_percentage;
    }
}