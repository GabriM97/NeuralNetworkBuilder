<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use App\Dataset;
use App\Network;
use App\Training;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'email', 'password', 'available_space',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function get_max_available_space(){
        switch ($this->rank) {
            case -1:  // ADMIN
                return 1073741824000;   // 1000 GB
                break;
            case 0:  // Base
                return 2147483648;   // 2 GB
                break;
            case 1:  // Advanced
                return 10737418240;   // 10 GB
                break;
            case 2:  // Professional
                return 32212254720;   // 30 GB
                break;
            
            default:
                return 0;
                break;
        }
    }

    public function get_tot_files_size(){
        $tot_datasets_size = Dataset::where("user_id", $this->id)
                                        ->sum("file_size");
        $tot_models_size = Network::where("user_id", $this->id)
                                        ->sum("file_size");
        $tot_size = $tot_datasets_size + $tot_models_size;
        return $tot_size;
    }

    public function getRank(){
        if($this->rank == -1)   return "admin";
        if($this->rank == 0)   return "base";
        if($this->rank == 1)   return "advanced";
        if($this->rank == 2)   return "professional";
    }

    public function getTrainingNumbers(){
        return Training::where("user_id", $this->id)->count();
    }
}
