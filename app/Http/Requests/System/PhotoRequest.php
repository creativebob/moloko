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
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:255',
            'link' => 'nullable|string',

            'avatar' => 'nullable|integer|max:1',

            'moderation' => 'nullable|integer|max:1',
            'system' => 'nullable|integer|max:1',
            'display' => 'nullable|integer|max:1',
        ];
    }
}
