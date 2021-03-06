<?php

namespace App\Http\Requests\System;

use Illuminate\Foundation\Http\FormRequest;

class BusinessCaseRequest extends FormRequest
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
            'description' => 'string|nullable',

            'display' => 'integer|max:1|nullable',
            'system' => 'integer|max:1|nullable',
            'moderation' => 'integer|max:1|nullable',
        ];
    }
}
