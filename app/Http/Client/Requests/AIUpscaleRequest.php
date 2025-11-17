<?php

declare(strict_types=1);

namespace App\Http\Client\Requests;

use App\Enums\Novita\NovitaModelUpscale;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

final class AIUpscaleRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'model_name' => ['required', new Enum(NovitaModelUpscale::class),],
            'image_base64' => ['required', 'string',],
            'scale_factor' => ['required', 'integer', 'min:1', 'max:4',],
        ];
    }
}
