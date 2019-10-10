<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Layer extends Model
{
    protected $fillable = ['model_id', 'layer_type', 'neurons_number', 'activation_function', 'is_output'];
}




