<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrainingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trainings', function (Blueprint $table) {
            $table->bigIncrements('id');
            
            // user
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')
                    ->references('id')->on('users')
                    ->onDelete('cascade');

            // model
            $table->unsignedBigInteger('model_id')->nullable();
            $table->foreign('model_id')
                    ->references('id')->on('networks')
                    ->onDelete('set null');

            // training dataset
            $table->unsignedBigInteger('dataset_id_training')->nullable();
            $table->foreign('dataset_id_training')
                    ->references('id')->on('datasets')
                    ->onDelete('set null');

            // test dataset
            $table->unsignedBigInteger('dataset_id_test')->nullable();
            $table->foreign('dataset_id_test')
                    ->references('id')->on('datasets')
                    ->onDelete('set null');
                    
            // training info
            $table->string("train_description")->nullable();
            $table->boolean('is_evaluated');
            $table->integer('epochs');
            $table->integer('executed_epochs')->default(0);     // only for resume-training usage
            $table->integer('batch_size');
            $table->float('validation_split', 3, 2)->default(0);    // A value between 0.0 and 1.0

            // training status
            $table->float('training_percentage', 3, 2)->default(0);     // A value between 0.0 and 1.0
            $table->enum('status', ['stopped', 'paused', 'started', 'error'])->default('stopped');
            $table->mediumText("return_message")->nullable()->default("Press the button below to start the training.");
            $table->boolean('in_queue')->default(false);
            $table->boolean('evaluation_in_progress')->default(false);
            $table->integer('process_pid')->nullable();

            // info model checkpoints
            $table->string('checkpoint_filepath')->nullable();
            $table->boolean('save_best_only')->default(0);      // Did I really need this?
            // $table->integer('period_between_checkpoints')->nullable();  // num of epochs

            $table->string('filepath_epochs_log')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trainings');
    }
}
