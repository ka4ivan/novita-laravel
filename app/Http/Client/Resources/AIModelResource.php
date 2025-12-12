<?php

namespace App\Http\Client\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

final class AIModelResource extends JsonResource
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
            'name' => $this->name,
            'base_name' => $this->base_name,
            'data' => $this->whenLoaded('data', fn () => AIDataResource::collection($this->data)),
        ];
    }
}
