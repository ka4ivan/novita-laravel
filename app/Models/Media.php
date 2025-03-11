<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Media extends \Spatie\MediaLibrary\MediaCollections\Models\Media
{
    use HasUuids;
}
