<?php

namespace App\Jobs;

use App\Training;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class TrainingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Delete the job if its models no longer exist. 
     */
    public $deleteWhenMissingModels = true;

    protected $training;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Training $training)
    {
        $this->training = $training;
    }

    /**
     * Execute the job.     // TrainingJob::dispatch($training);
     *
     * @return void
     */
    public function handle()
    {
        try {
            $process = new Process("ping -c 5 localhost");
			$process->mustRun();
            
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
    public function failed(\Exception $exception)
    {
        // Send user notification of failure, etc...
        $this->training->train_description = "FAILED";
        $this->training->save();
    }
}
