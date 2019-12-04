<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddServerNode extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('nodes')->insert([
            [
                'description' => 'Web Server',
                'ip_address' => '127.0.0.1',
                'is_webserver' => true,
                'created_at' => DB::raw('now()'),
                'updated_at' => DB::raw('now()'),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('nodes', function (Blueprint $table) {
            //
        });
    }
}
