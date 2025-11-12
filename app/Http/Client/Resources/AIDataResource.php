<?php

namespace App\Http\Client\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

final class AIDataResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'images' => $this->whenLoaded('media', fn () => MediaShowResource::collection($this->getMedia('image'))),
        ];
    }
}
