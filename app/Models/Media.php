<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Media extends \Spatie\MediaLibrary\MediaCollections\Models\Media
{
    use HasUuids;

    protected $guarded = [
        'id',
    ];
}
