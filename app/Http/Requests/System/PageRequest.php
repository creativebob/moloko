<?php

namespace App\Http\Requests\System;

use Illuminate\Foundation\Http\FormRequest;

class PageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'alias' => 'string|max:255|nullable',

            'description' => 'string|nullable',
            'content' => 'string|nullable',

            'photo_id' => 'integer|nullable',

            'display' => 'integer|max:1|nullable',
            'system' => 'integer|max:1|nullable',
            'moderation' => 'integer|max:1|nullable',
        ];
    }
}
