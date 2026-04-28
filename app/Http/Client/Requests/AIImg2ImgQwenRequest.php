<?php

namespace App\Http\Client\Requests;

use App\Enums\Qwen\QwenOutputFormat;
use App\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

final class AIImg2ImgQwenRequest extends FormRequest
{
    public function rules()
    {
        return [
            'image' => ['required', 'string'],
            'prompt' => ['required', 'string', 'between:1,1024'],
            'seed' => ['nullable', 'numeric', 'between:-1,2147483647'],
            'output_format' => ['nullable', 'string', new Enum(QwenOutputFormat::class)],
        ];
    }

    public function getData()
    {
        return $this->only([
            'image',
            'prompt',
            'seed',
            'output_format',
        ]);
    }
}
