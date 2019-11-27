<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use App\User;

class AddDefaultUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('users')->insert([
            [
                'username' => 'admin',
                'email' => 'admin@admin.com',
                'password' => Hash::make("ciao1234"),
                'rank' => '-1',
                'available_space' => 1073741824000,
                'created_at' => DB::raw('now()'),
                'updated_at' => DB::raw('now()'),
            ]
        ]);

        $this->init_user_folders();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }

    /**
    * Create the user default folders.
    *
    * @return void
    */
    private function init_user_folders(){
        $user = User::all()->last();

        $hashed_user = hash("md5", $user->id); 
        $user_dir_datasets = "users/$hashed_user/datasets";
        $user_dir_models = "users/$hashed_user/models";
        $user_dir_trainings = "users/$hashed_user/trainings";
        
        // make "private" directories
        Storage::makeDirectory($user_dir_datasets);
        Storage::setVisibility($user_dir_datasets, 'public');
        
        Storage::makeDirectory($user_dir_models);
        Storage::setVisibility($user_dir_models, 'public');
        
        Storage::makeDirectory($user_dir_trainings);
        Storage::setVisibility($user_dir_trainings, 'public');
        
        // make public directories
        Storage::makeDirectory("public/$user_dir_datasets");
        Storage::setVisibility("public/$user_dir_datasets", 'public');
        
        Storage::makeDirectory("public/$user_dir_models");
        Storage::setVisibility("public/$user_dir_models", 'public');
        
        Storage::makeDirectory("public/$user_dir_trainings");
        Storage::setVisibility("public/$user_dir_trainings", 'public');
    }
}