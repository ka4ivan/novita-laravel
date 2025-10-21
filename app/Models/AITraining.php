<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class AITraining extends Model
{
    use HasUuids;

    const STATUS_WAITING = 'waiting'; // Щойно створене | очікує
    const STATUS_DONE = 'done'; // Виконано
    const STATUS_FAILED = 'failed'; // Неуспішно

    const TYPE_TXT2IMG = 'txt2img';
    const TYPE_IMG2IMG = 'img2img';

    protected $table = 'ai_trainings';

    protected $guarded = [
        'id',
    ];

    protected $attributes = [
        'status' => self::STATUS_WAITING,
    ];

    protected function casts(): array
    {
        return [
            'options' => 'array',
            'extra' => 'array',
        ];
    }
}
