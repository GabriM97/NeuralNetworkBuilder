<?php

namespace App\Jobs;

use App\Training;
use App\Network;
use App\Dataset;
use App\User;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Exception;

class TrainingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const THROW_DEFAULT = 0;
    const THROW_ALREADYSTARTED = 1;
    const THROW_ONERROR = 2;
    const THROW_SETPAUSE = 3;
    const THROW_STOPPROCESS = 4;

    /**
     * Delete the job if its models no longer exist. 
     */
    public $deleteWhenMissingModels = true;
    public $timeout = 86390; // almost 24 hours

    protected $training;
    protected $user;
    protected $model;
    protected $dataset_training;
    protected $dataset_test;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Training $training, User $user, Network $network, Dataset $dataset_train, Dataset $dataset_test = NULL)
    {
        $this->training = $training;
        $this->user = $user;
        $this->model = $network;
        $this->dataset_training = $dataset_train;
        if($dataset_test)
            $this->dataset_test = $dataset_test;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        
        try {
            // check for errors
            if($this->training->status == "error")  throw new Exception("Cannot start/resume: Training is on error status.", self::THROW_ONERROR);
            if($this->training->status == "started") throw new Exception("Cannot start/resume: Training already started.", self::THROW_ALREADYSTARTED);

            // Update "last_time_used"
            $this->model->last_time_used = Carbon::now();
            $this->model->update();

            $this->dataset_training->last_time_used = Carbon::now();
            $this->dataset_training->update();

            if($this->dataset_test){
                $this->dataset_test->last_time_used = Carbon::now();
                $this->dataset_test->update();
            }
            
            // Check if it's a new start (or it's resume) and set its percentage to 0%
            if($this->training->status == "stopped"){
                $this->training->training_percentage = 0;
                $this->training->executed_epochs = 0;
            }

            // Start the training
            if(!$this->training->evaluation_in_progress){
                $this->training->startTraining($this->user, $this->model, $this->dataset_training);
                $this->training->process_pid = NULL;
                $this->training->update();
            }

            // Evaluate the model
            if($this->training->is_evaluated){
                $this->training->evaluation_in_progress = True;
                $this->training->update();
                $this->model->evaluateModel($this->user, $this->training, $this->dataset_test);
                $this->training->process_pid = NULL;
                $this->training->evaluation_in_progress = False;
                $this->training->update();
            }

            // Update status and return message
            $this->training->status = "stopped";
            $this->training->return_message = "Training successfully completed.";
            $this->training->in_queue = false;
            $this->training->update();

            //delete model from checkpoint path
            Storage::delete($this->training->checkpoint_filepath."model_".$this->training->model_id.".h5");

        } catch (Exception $err) {
            $this->on_fail($err);
        }
    }

    /**
     * Handle job on fail.
     *
     * @param  Exception  $exception
     * @return void
     */
    public function on_fail(Exception $exception)
    {
        // Training ALREADY STARTED Exception
        if($exception->getCode() == self::THROW_ALREADYSTARTED){
            $this->training->return_message = $exception->getMessage()." If you want to start a new training, stop this or create a new one.";
            $this->training->update();
            throw $exception;
        }

        // Training ON ERROR status Exception
        if($exception->getCode() == self::THROW_ONERROR){
            // Read ERROR MESSAGE
            if(strpos($this->training->return_message, "ERROR MESSAGE:"))
                $old_err_msg = strstr($this->training->return_message, "ERROR MESSAGE:");
            else
                $old_err_msg = "\nERROR MESSAGE: ".$this->training->return_message;

            $this->training->return_message = $exception->getMessage()." If you want you can create a new training. $old_err_msg";
            $this->training->in_queue = false;
            $this->training->evaluation_in_progress = false;
            $this->training->update();
            throw $exception;
        }

        // Training SET PAUSE status Exception
        if($exception->getCode() == self::THROW_SETPAUSE){
            $this->training->return_message = $exception->getMessage();
            $this->training->status = "paused";
            $this->training->in_queue = false;
            $this->training->process_pid = NULL;
            $this->training->update();
            //throw $exception;     // do not fail the job
        }
        
        // Training STOP PROCESS status Exception
        if($exception->getCode() == self::THROW_STOPPROCESS){
            $this->training->return_message = $exception->getMessage();
            $this->training->status = "stopped";
            $this->training->in_queue = false;
            $this->training->evaluation_in_progress = false;
            $this->training->process_pid = NULL;
            $this->training->update();
            //throw $exception;     // do not fail the job
        }

        // Other Exceptions
        if($exception->getCode() == self::THROW_DEFAULT){
            $this->training->return_message = $exception->getMessage();
            $this->training->status = "error";
            $this->training->in_queue = false;
            $this->training->evaluation_in_progress = false;
            $this->training->process_pid = NULL;
            $this->training->update();

            //delete model from checkpoint path
            Storage::delete($this->training->checkpoint_filepath."model_".$this->training->model_id.".h5");

            throw $exception;
        }
    }
}
