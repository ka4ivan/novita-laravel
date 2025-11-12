<?php

namespace App\Models;

use Fomvasss\MediaLibraryExtension\HasMedia\HasMedia;
use Fomvasss\MediaLibraryExtension\HasMedia\InteractsWithMedia;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class AITrainingData extends Model implements HasMedia
{
    use HasUuids,
        InteractsWithMedia;

    protected $table = 'ai_training_data';

    protected $guarded = [
        'id',
    ];

    protected array $mediaSingleCollections = ['image'];
}
