<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLayersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::enableForeignKeyConstraints();

        Schema::create('layers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('model_id');
            $table->foreign('model_id')
                    ->references('id')->on('networks')
                    ->onDelete('cascade');             //parameters to define
            $table->enum('layer_type', ['dense', 'dropout', 'flatten', 'reshape'])->default('dense');
            $table->integer('neurons_number');
            $table->enum('activation_function', ['relu', 'sigmoid', 'tanh', 'linear', 'softmax']);
            $table->boolean('is_output')->default(false);
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
        Schema::dropIfExists('layers');
    }
}
