<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Node extends Model
{
    protected $fillable = ['ip_address', 'description',];
    
    // Get HW info
    public function getNodeHwInfo(){
        try {
            $output_page = file_get_contents("http://$this->ip_address:5050/getHardwareInfo");
            if($output_page === false)
                return array("status" => 0);
        } catch (\Throwable $th) {
            return array("status" => 0);
        }

        return json_decode($output_page, true);
    }
}