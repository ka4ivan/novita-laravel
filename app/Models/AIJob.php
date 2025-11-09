<?php

namespace App\Models;

use Fomvasss\MediaLibraryExtension\HasMedia\HasMedia;
use Fomvasss\MediaLibraryExtension\HasMedia\InteractsWithMedia;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Ka4ivan\LaravelLogger\Models\Traits\HasTracking;

class AIJob extends Model implements HasMedia
{
    use HasUuids,
        InteractsWithMedia,
        HasTracking;

    const STATUS_WAITING = 'waiting'; // Щойно створене | очікує
    const STATUS_DONE = 'done'; // Виконано
    const STATUS_FAILED = 'failed'; // Неуспішно

    const TYPE_TXT2IMG = 'txt2img';
    const TYPE_IMG2IMG = 'img2img';

    protected $table = 'ai_jobs';

    protected $guarded = [
        'id',
    ];

    protected $attributes = [
        'status' => self::STATUS_WAITING,
        'type' => self::TYPE_TXT2IMG,
    ];

    protected array $mediaSingleCollections = ['image'];
    protected array $mediaMultipleCollections = ['images'];

    protected function casts(): array
    {
        return [
            'options' => 'array',
            'extra' => 'array',
        ];
    }
}
