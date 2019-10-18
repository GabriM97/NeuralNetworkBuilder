<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Training extends Model
{
    protected $fillable = [
        'user_id', 'model_id', 'dataset_id_training', 'dataset_id_test', 'is_evaluated', 'epochs', 'batch_size', 'validation_split',
        'checkpoint_filepath', 'save_best_only', 'filepath_epochs_log',
    ];
}
