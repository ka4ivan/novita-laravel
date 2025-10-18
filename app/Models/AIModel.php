<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class AIModel extends Model
{
    use HasUuids;

    protected $table = 'ai_models';

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
