<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->tinyInteger('rank')->default(0)->comment('-1: admin | 0: user | 1: advanced | 2: professional');
            $table->integer('models_number')->default(0);
            $table->integer('datasets_number')->default(0);
            $table->unsignedBigInteger('available_space')->default(2147483648);     // user: 2 GB | advanced: 10 GB (+8GB) | professional: 30 GB (+28GB or +20GB)
            $table->timestamp('last_signed_on')->useCurrent();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
