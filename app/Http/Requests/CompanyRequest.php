<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompanyRequest extends FormRequest
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

            'company_name' => 'string|max:255|required', 
            'company_alias' => 'string|max:255|alpha', 
            'phone' => 'string|max:17|required', 
            'extra_phone' => 'string|max:17|nullable', 
            'email' => 'nullable|string|email|max:255', 
            'city_id' => 'integer|nullable', 
            'address' => 'string|max:255|nullable', 

            'account_settlement' => 'string|nullable', 
            'account_correspondent' => 'string|nullable', 
            'bank' => 'string|max:255|nullable', 
            'inn' => 'max:12|nullable', 
            'kpp' => 'max:255|nullable',

            'moderation' => 'integer|max:1|nullable',
            'system_item' => 'integer|max:1|nullable',
              
        ];
    }
}
