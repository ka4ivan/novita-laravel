<?php

namespace App\Http\Client\Requests;

use App\Http\FormRequest;

final class AITxt2ImgGeminiRequest extends FormRequest
{
    public function rules()
    {
        return [
            'prompt' => ['required', 'string', 'between:1,1024'],
        ];
    }

    public function getData()
    {
        return $this->only([
            'prompt',
        ]);
    }
}
