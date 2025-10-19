<?php

namespace App\Http\Client\Requests;

use App\Http\FormRequest;

final class ProfileRequest extends FormRequest
{
    public function rules()
    {
        $id = $this->user()?->id;

        $res = [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'lastname' => ['nullable', 'string', 'max:255'],
            'email' => ['sometimes', 'email:strict', "unique:users,email,{$id}", 'min:3', 'max:200'],
        ];

        return $res;
    }
}
