<?php

namespace App\Http\Requests;

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
            'date_employment' => 'date|after:01.01.1940', 
            'date_dismissal' => 'date|after:01.01.1940|nullable', 
            'dismissal_desc' => 'string|nullable', 
        ];
    }
}
