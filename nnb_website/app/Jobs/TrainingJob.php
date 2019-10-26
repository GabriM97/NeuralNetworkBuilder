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
use Carbon\Carbon;
use Exception;

class TrainingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const THROW_DEFAULT = 0;
    const THROW_ALREADYSTARTED = 1;
    const THROW_ONERROR = 2;

    /**
     * Delete the job if its models no longer exist. 
     */
    public $deleteWhenMissingModels = true;

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
    public function __construct(Training $training, User $user, Network $network, Dataset $dataset_train, Dataset $dataset_test)
    {
        $this->training = $training;
        $this->user = $user;
        $this->model = $network;
        $this->dataset_training = $dataset_train;
        if(isset($dataset_test))
            $this->dataset_test = $dataset_test;
        else 
            $this->dataset_test = NULL;
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
            
            // Check if it's a new start and set its percentage to 0%
            if($this->training->status == "stopped")
                $this->training->training_percentage = 0;
            
            // Set status and return message
            $this->training->status = "started";
            $this->training->return_message = "Training in progress...";
            $this->training->update();

            // Start the training
            $this->training->startTraining($this->model, $this->dataset_training);

            // Evaluate the model
            if($this->training->is_evaluated)
                $this->model->evaluateModel($this->dataset_test);


            // Update status and return message
            $this->training->status = "stopped";
            $this->training->return_message = "Training successfully completed.";
            $this->training->update();

            $this->model->is_trained = true;
            // DO NOT FORGET TO SET THE ACCURACY (set accuracy after each epochs or at training stops?)

        } catch (\Throwable $th) {
            $this->on_fail($th);
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
        // Training already started Exception
        if($exception->getCode() == self::THROW_ALREADYSTARTED){
            $this->training->return_message = $exception->getMessage()." If you want to start a new training, stop this or create a new one.";
            $this->training->update();
            throw $exception;
        }

        // Training on error status Exception
        if($exception->getCode() == self::THROW_ONERROR){
            
            // substr from "ERROR MESSAGE:" till end of string
            if(strpos($this->training->return_message, "ERROR MESSAGE:"))
                $old_err_msg = strstr($this->training->return_message, "ERROR MESSAGE:");
            else
                $old_err_msg = "\nERROR MESSAGE: ".$this->training->return_message;

            $this->training->return_message = $exception->getMessage()." If you want you can create a new training. $old_err_msg";
            $this->training->update();
            throw $exception;
        }

        // Other Exceptions
        if($exception->getCode() == self::THROW_DEFAULT){
            $this->training->return_message = $exception->getMessage();
            $this->training->status = "error";
            $this->training->update();
            throw $exception;
        }
    }
}
