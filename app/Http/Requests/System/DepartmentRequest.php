<?php

namespace App\Http\Requests\System;

use Illuminate\Foundation\Http\FormRequest;

class DepartmentRequest extends FormRequest
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
            'address' => 'nullable|string',
            'email' => 'nullable|string|email|max:255',

            'main_phone' => 'nullable|string|max:17',
            'extra_phones.*' => 'nullable|string|max:17',

            'cities.*' => 'nullable|integer',

            'filial_id' => 'nullable|integer',
            'department_id' => 'nullable|integer',
            'parent_id' => 'nullable|integer',

            'code_map' => 'nullable|string',

            'city_id' => 'nullable|integer',

            'moderation' => 'nullable|integer|max:1',
            'system' => 'nullable|integer|max:1',
            'display' => 'nullable|integer|max:1',
        ];
    }
}
