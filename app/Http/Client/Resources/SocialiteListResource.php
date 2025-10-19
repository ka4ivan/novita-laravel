<?php

namespace App\Http\Client\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

final class SocialiteListResource extends JsonResource
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
            'provider' => $this->provider,
            'provider_id' => $this->provider_id,
        ];
    }
}
