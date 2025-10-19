<?php

namespace App\Http\Client\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

final class ProfileResource extends JsonResource
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
            'lastname' => $this->lastname,
            'email' => $this->email,
            'phone' => $this->phone,
            'balance' => $this->balance,
            'status' => $this->status,
            'created_at' => $this->getDatetime('created_at'),
            'avatar' => $this->whenLoaded('media', fn () => MediaShowResource::make($this->getMainMedia('avatar'))),
            'socialites' => SocialiteListResource::collection($this->whenLoaded('socialites')),
        ];
    }
}
