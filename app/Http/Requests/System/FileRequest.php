<?php

namespace App\Http\Requests\System;

use Illuminate\Foundation\Http\FormRequest;

class FileRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string',
            'description' => 'nullable|string',
            'title' => 'nullable|string',

            // 'file' => 'file',
        ];
    }
}
