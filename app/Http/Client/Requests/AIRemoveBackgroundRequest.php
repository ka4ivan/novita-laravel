<?php

declare(strict_types=1);

namespace App\Http\Client\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class AIRemoveBackgroundRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'image_file' => [
                'required',
                'string',
            ],
        ];
    }
}
