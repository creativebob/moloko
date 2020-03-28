<?php

namespace App\Http\Requests\System;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeRequest extends FormRequest
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
            'staffer_id' => 'required|integer|exists:staff,id',

            'employment_date' => 'required|date|after:01.01.1940',
            'dismissal_date' => 'nullable|date|after:01.01.1940',
            'dismissal_description' => 'nullable|string',

            'display' => 'nullable|integer|max:1',
            'moderation' => 'nullable|integer|max:1',
            'system' => 'nullable|integer|max:1',
        ];
    }
}
