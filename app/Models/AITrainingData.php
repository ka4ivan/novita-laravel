<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class AITrainingData extends Model
{
    use HasUuids;

    protected $table = 'ai_training_data';

    protected $guarded = [
        'id',
    ];
}
