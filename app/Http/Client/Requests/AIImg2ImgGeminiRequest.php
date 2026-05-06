<?php

namespace App\Http\Client\Requests;

use App\Enums\Gemini\GeminiAspectRatio;
use App\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

final class AIImg2ImgGeminiRequest extends FormRequest
{
    public function rules()
    {
        return [
            'image_base64s' => ['required', 'array'],
            'image_base64s.*' => ['required', 'file'],
            'prompt' => ['required', 'string', 'between:1,1024'],
            'aspect_ratio' => ['nullable', new Enum(GeminiAspectRatio::class)],
        ];
    }

    public function getData()
    {
        return $this->only([
            'prompt',
            'aspect_ratio',
        ]);
    }
}
