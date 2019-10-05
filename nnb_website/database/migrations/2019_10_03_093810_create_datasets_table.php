<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDatasetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::enableForeignKeyConstraints();

        Schema::create('datasets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')
                    ->references('id')->on('users')
                    ->onDelete('cascade');
            $table->string('data_name')->default("Dataset");    //as Data Title
            $table->string('data_description')->nullable();
            $table->unsignedInteger('file_size');        //in Bytes
            $table->string('file_extension');
            $table->integer('x_shape');         //input size
            $table->integer('y_classes');       //output size
            $table->string('local_path');       //  users/hash(user_id)/datasets/dataset_id.***
            $table->boolean('is_train')->default(false);
            $table->boolean('is_test')->default(false);
            $table->boolean('is_generic')->default(true);
            $table->timestamp('last_time_used')->nullable();
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
        Schema::dropIfExists('datasets');
    }
}
