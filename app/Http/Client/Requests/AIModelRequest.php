<?php

namespace App\Http\Client\Requests;

use App\Enums\Novita\NovitaModelType;
use App\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

final class AIModelRequest extends FormRequest
{
    public function rules()
    {
        return [
            'q' => ['nullable', 'string',],
            'type' => ['required', new Enum(NovitaModelType::class),],
            'sdxl' => ['nullable', 'boolean',],
            'amount' => ['nullable', 'integer', 'between:1,100',],
            'cursor' => ['nullable', 'string',],
        ];
    }
}
