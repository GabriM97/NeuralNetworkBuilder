<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Dataset extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'data_name', 'data_description', 'file_size', 'file_extension', 'x_shape', 'y_classes', 'local_path', 'is_train', 'is_test', 'is_generic',
    ];
}
