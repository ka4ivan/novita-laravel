<?php

namespace App\Http\Client\Requests;

use App\Enums\Novita\NovitaSampler;
use App\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

final class AITxt2ImgRequest extends FormRequest
{
    public function rules()
    {
        return [
            'model_name' => ['required', 'string', 'max:255'],
            'prompt' => ['required', 'string', 'between:1,1024'],
            'negative_prompt' => ['string', 'between:1,1024'],
            'loras' => ['array', 'max:5'],
            'loras.*.model_name' => ['required', 'string', 'max:255'],
            'loras.*.strength' => ['required', 'numeric', 'between:0,1'],
            'refiner' => ['array'],
            'refiner.switch_at' => ['required_with:refiner', 'numeric', 'between:0,1'],
            'width' => ['required', 'integer', 'between:128,2048'],
            'height' => ['required', 'integer', 'between:128,2048'],
            'image_num' => ['required', 'integer', 'between:1,8'],
            'steps' => ['required', 'integer', 'between:1,100'],
            'seed' => ['required', 'integer', 'min:-1'],
            'clip_skip' => ['integer', 'between:1,12'],
            'guidance_scale' => ['required', 'numeric', 'between:1,30'],
            'sampler_name' => ['required', new Enum(NovitaSampler::class)],
        ];
    }

    public function getData()
    {
        return $this->only([
            'model_name',
            'prompt',
            'negative_prompt',
            'width',
            'height',
            'image_num',
            'steps',
            'seed',
            'clip_skip',
            'guidance_scale',
            'sampler_name',
            'refiner',
        ]);
    }
}
