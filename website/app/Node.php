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

    // Get Lightest Node (based on %CPU, %RAM, CPU_cores, tot_RAM)
    public static function getLightestNode(){
        $min_active_node = NULL;
        $min_node_info = NULL;
        $min_usage_ratio = NULL;    //(%CPU*2 + %RAM*1)/3     [0 - 100]     the smallest -> the lightest
        
        $nodes = Node::all();
        foreach ($nodes as $node) {
            $set_new = false;
            $info = $node->getNodeHwInfo();
            if($info["status"]){    //is ON
                $ratio = (($info["cpu"]["usage"]*2) + ($info["ram"]["usage"]*1))/3; //calculate ratio
                if($min_active_node === NULL)     //first node
                    $set_new = true;
                else
                    if($ratio < $min_usage_ratio)  //compare ratio
                        $set_new = true;
                    elseif ($ratio == $min_usage_ratio) 
                        if($info['cpu']["threads"] > $min_node_info['cpu']["threads"]) //compare cores
                            $set_new = true;
                        elseif ($info['cpu']["threads"] == $min_node_info['cpu']["threads"])
                            if($info['ram']["total"] > $min_node_info['ram']["total"]) //compare tot ram
                                $set_new = true;
                
                if($set_new){
                    $min_active_node = $node;
                    $min_node_info = $info;
                    $min_usage_ratio = $ratio;
                }
            }
        }
        return $min_active_node;
    }
}