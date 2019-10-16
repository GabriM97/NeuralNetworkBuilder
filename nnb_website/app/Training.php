<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Training extends Model
{
    protected $fillable = [
        'model_id', 'dataset_id_training', 'dataset_id_test', 'is_evaluated', 'epochs', 'batch_size', 'validation_split', /*training_status,*/
        'checkpoint_filepath', 'save_best_only', 'filepath_epochs_log',
    ];
}
