<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNetworksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('networks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')
                    ->references('id')->on('users')
                    ->onDelete('cascade');
            $table->enum('model_type', ['Sequential', 'Functional'])->default("Sequential");
            $table->integer('input_shape');
            $table->integer('layers_number')->default(1);   // min 1 = output_layer
            $table->integer('output_classes');
            $table->boolean('is_compiled')->default(false);
            $table->boolean('is_trained')->default(false);
            $table->float('accuracy', 4, 2)->nullable();
            $table->float('loss', 4, 2)->nullable();
            $table->string('model_name')->default("Model");
            $table->string('model_description')->nullable();
            $table->unsignedInteger('file_size');	//	in Bytes
            $table->string('local_path');	//	users/hash(user_id)/models/model_id.h5
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
        Schema::dropIfExists('networks');
    }
}
