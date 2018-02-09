<?php

namespace App\Http\Requests;

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
            'filial_database' => 'integer|nullable', 
            'city_name' => 'string|max:255|nullable',
            'city_id' => 'integer|nullable', 
            'filial_name' => 'string|max:255|nullable',
            'filial_address' => 'string|max:255|nullable',
            'filial_phone' => 'string|max:11|nullable',
            'filial_id' => 'integer|nullable',  
            'department_database' => 'integer|nullable', 
            'department_name' => 'string|max:255|nullable',
            'department_address' => 'string|max:255|nullable',
            'department_phone' => 'string|max:11|nullable', 

            'parent_id' => 'integer|nullable', 
        ];
    }
}
