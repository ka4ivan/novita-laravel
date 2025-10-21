<?php

namespace App\Http\Client\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;

final class MediaShowResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $fullUrl = $this->getFullUrl(isset($this->generated_conversions['optimize']) ? 'optimize' : '');

        return [
            'id' => $this->id,
            'name' => $this->name,
            'url' => $fullUrl,
            'alt' => $this->when(!is_null($this->alt), fn() => $this->alt),
            'conversions' => $this->when(boolval($a = $this->getConversions()) , fn() => $a),
        ];
    }

    /**
     * @return array
     */
    public function getConversions(): array
    {
        $conversions = array_keys($this->generated_conversions ?? []);
        $res = [];

        foreach ($conversions as $conversion) {
            if (in_array($conversion, ['og_image', 'google_merchant'])) {
                continue;
            }
            try {
                $res[$conversion] = [
                    'url' => $this->getFullUrl($conversion),
                ];
            } catch (\Exception $exception) {
                Log::error($exception->getMessage());
            }
        }

        return $res;
    }
}
