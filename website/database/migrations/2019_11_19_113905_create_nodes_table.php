<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nodes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('description')->nullable();
            $table->boolean('status')->default(0);  // OFF
            $table->ipAddress('ip_address')->unique();  // kk.jj.xx.yy
            $table->string('cpu_description')->nullable();
            $table->tinyInteger('cpu_numbers')->nullable();
            $table->string('gpu_details')->nullable();
            $table->unsignedBigInteger('total_ram')->nullable();
            $table->integer('running_trainings')->default(0);
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
        Schema::dropIfExists('nodes');
    }
}
