<?php

namespace App\Http\Requests\System;

use Illuminate\Foundation\Http\FormRequest;

class PhotoRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            // 'photo' => 'file',
            'title' => 'string|max:255|nullable',
            'description' => 'string|nullable',
            'color' => 'string|max:255|nullable',
            'link' => 'string|nullable',

            'avatar' => 'integer|max:1|nullable',

            'moderation' => 'integer|max:1|nullable',
            'system' => 'integer|max:1|nullable',
            'display' => 'integer|max:1|nullable',
        ];
    }
}