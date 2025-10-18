<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class AITraining extends Model
{
    use HasUuids;

    protected $table = 'ai_trainings';

    protected $guarded = [
        'id',
    ];

    protected function casts(): array
    {
        return [
            'options' => 'array',
            'extra' => 'array',
        ];
    }
}
