<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class AIModel extends Model
{
    use HasUuids;

    const STATUS_CREATED = 'created'; // Створено
    const STATUS_QUEUING = 'queuing'; // Очікування в черзі
    const STATUS_TRAINING = 'training'; // Навчання
    const STATUS_DEPLOYING = 'deploying'; // Розгортання
    const STATUS_SUCCESS = 'success'; // Успішно
    const STATUS_CANCELED = 'canceled'; // Скасовано
    const STATUS_FAILED = 'failed'; // Неуспішно

    protected $table = 'ai_models';

    protected $guarded = [
        'id',
    ];

    protected $attributes = [
        'status' => self::STATUS_CREATED,
    ];

    protected function casts(): array
    {
        return [
            'options' => 'array',
            'extra' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function data(): MorphMany
    {
        return $this->morphMany(AITrainingData::class, 'model');
    }
}
