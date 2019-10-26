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
            $this->model->last_time_used = Carbon::now();
            $this->dataset_training->last_time_used = Carbon::now();
            if($this->dataset_test)     $this->dataset_test->last_time_used = Carbon::now();
            
            $this->model->update();
            $this->dataset_training->update();
            if($this->dataset_test)     $this->dataset_test->update();

            if($this->training->status == "error")  throw new Exception("Cannot start/resume:", self::THROW_ONERROR);
            if($this->training->status == "started") throw new Exception("Cannot start/resume:", self::THROW_ALREADYSTARTED);
            
            if($this->training->status == "stopped")
                $this->training->training_percentage = 0;
                
            $this->training->status = "started";
            $this->training->return_message = "Training started.";
            $this->training->update();

            /*$process = new Process("timeout 30 tail -f /home/gabri/Desktop/to-do.txt");
            $process->mustRun();*/
            for ($i=0; $i<10; $i++) { 
                sleep(1);
                $this->training->training_percentage += 0.1;
                $this->training->update();
            }
            
            $this->training->status = "stopped";
            $this->training->return_message = "Training successfully completed.";
            $this->training->update();

        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * The job failed to process.
     *
     * @param  Exception  $exception
     * @return void
     */
    public function failed(Exception $exception)
    {
        if($exception->getCode() == self::THROW_ALREADYSTARTED){
            $this->training->return_message = "$exception->getMessage() Training already started. If you want to start a new training, stop this or create a new one.";
            $this->training->update();
            return;
        }
        if($exception->getCode() == self::THROW_ONERROR){
            $this->training->return_message = "$exception->getMessage() Training is on error status. If you want you can create a new training.";
            $this->training->update();
            return;
        }
        if($exception->getCode() == self::THROW_DEFAULT){
            $this->training->return_message = "$exception->getMessage()";
            $this->training->update();
            return;
        }
    }
}
