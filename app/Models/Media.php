<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Facades\Storage;

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

    /**
     * Повертає файл у Base64.
     */
    public function toBase64(bool $withPrefix = true): ?string
    {
        try {
            $disk = Storage::disk($this->disk);

            if (!$disk->exists($this->getPathRelativeToRoot())) {
                return null;
            }

            $data = $disk->get($this->getPathRelativeToRoot());
            $base = base64_encode($data);

            if (! $withPrefix) {
                return $base;
            }

            return "data:{$this->mime_type};base64,{$base}";
        } catch (\Throwable $e) {
            \Llog::error('Media::toBase64 failed', [
                'media_id' => $this->id,
                'error'    => $e->getMessage(),
            ]);

            return null;
        }
    }
}
