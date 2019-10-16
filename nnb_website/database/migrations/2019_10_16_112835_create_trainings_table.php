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
                    ->references('id')->on('users');

            // model
            $table->unsignedBigInteger('model_id');
            $table->foreign('model_id')
                    ->references('id')->on('networks');

            // training dataset
            $table->unsignedBigInteger('dataset_id_training');
            $table->foreign('dataset_id_training')
                    ->references('id')->on('datasets');

            // test dataset
            $table->unsignedBigInteger('dataset_id_test')->nullable();
            $table->foreign('dataset_id_test')
            ->references('id')->on('datasets');
                    
            $table->boolean('is_evaluated');
            $table->integer('epochs');
            $table->integer('batch_size');
            $table->float('validation_split', 4, 2)->default(0);
            $table->float('training_status', 2, 2)->default(0);     // percentage of training. A value between 0.0 and 1.0

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
