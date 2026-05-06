<?php

namespace App\Http\Client\Requests;

use App\Enums\Gemini\GeminiAspectRatio;
use App\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

final class AITxt2ImgGeminiRequest extends FormRequest
{
    public function rules()
    {
        return [
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
