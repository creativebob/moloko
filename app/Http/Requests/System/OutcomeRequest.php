<?php

namespace App\Http\Requests\System;

use Illuminate\Foundation\Http\FormRequest;

class OutcomeRequest extends FormRequest
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
            'description' => 'nullable|string',

            'category_id' => 'required|integer',
            'client_id' => 'required|integer',

            'begin_date' => 'required|date|after:01.01.2015',
            'end_date' => 'nullable|date|after:01.01.2015',

            'display' => 'nullable|integer|max:1',
            'moderation' => 'nullable|integer|max:1',
            'system' => 'nullable|integer|max:1',
        ];
    }
}
