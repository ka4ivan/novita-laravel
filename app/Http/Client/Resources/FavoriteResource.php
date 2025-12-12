<?php

namespace App\Http\Client\Resources;

use App\Models\Media;
use Illuminate\Http\Resources\Json\JsonResource;

final class FavoriteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $res = [];

        switch (true) {
            case $this->model instanceof Media:
                $res = new MediaShowResource($this->whenLoaded('model'));
                break;
        }

        return $res;
    }
}
