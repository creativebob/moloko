<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCompany extends FormRequest
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
            'company_phone' => 'string|max:17|required', 
            'company_extra_phone' => 'string|max:17|nullable', 
            'city_id' => 'integer|nullable', 
            'company_address' => 'string|max:255|nullable', 

            'account_settlement' => 'string|nullable', 
            'account_correspondent' => 'string|nullable', 

            // Вариант если требуеться в обязательном порядке всем организациям указывать платежные реквизиты
            // 'account_settlement' => 'string|required_if:orgform_status, 1|nullable', 
            // 'account_correspondent' => 'string|required_if:orgform_status, 1|nullable', 

            'company_inn' => 'max:12|nullable', 
            'kpp' => 'max:255|nullable', 
        ];
    }
}
