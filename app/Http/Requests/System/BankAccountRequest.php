<?php

namespace App\Http\Requests\System;

use Illuminate\Foundation\Http\FormRequest;

class BankAccountRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [

            // 'booklists_name' => 'string|max:255', 
            // 'booklists_description' => 'string|max:255|nullable', 
            
        ];
    }
}
