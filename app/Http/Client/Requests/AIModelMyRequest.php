<?php

namespace App\Http\Client\Requests;

use App\Enums\Novita\NovitaTrainingBaseModel;
use App\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

final class AIModelMyRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => [
                'required',
                'string',
                'unique:ai_models,name',
                'max:255',
            ],
            'base_model' => [
                'required',
                new Enum(NovitaTrainingBaseModel::class),
            ],
            'files' => ['required', 'array', 'min:1'],
            'caption' => ['required', 'array', 'min:1'],
        ];
    }

    public function getData()
    {
        return $this->only('name', 'base_model');
    }
}
