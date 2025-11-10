<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Media extends \Spatie\MediaLibrary\MediaCollections\Models\Media
{
    use HasUuids;

    protected $guarded = [
        'id',
    ];

    /**
     * @return bool
     */
    public function isFavorite(): bool
    {
        return \Favorite::isFavorite($this);
    }

    public function getClientStates(): array
    {
        return [
            'is_favorite' => $this->isFavorite(),
        ];
    }
}
