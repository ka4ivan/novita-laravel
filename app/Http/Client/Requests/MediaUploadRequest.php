<?php

namespace App\Http\Client\Requests;

use App\Http\FormRequest;

final class MediaUploadRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'file' => ['required_without:url', 'nullable', 'file', 'mimes:mp4,mov,ogg,qt,flv,3gp,mov,avi,wmv,jpeg,jpg,png,bmp,gif,svg,webm',],
            'url' => ['required_without:file', 'nullable', 'url',],
            'is_main' => ['nullable', 'string',],
            'collection_name' => ['nullable', 'string',],
        ];
    }
}
