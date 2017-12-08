<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUser extends FormRequest
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

            'login' => 'required|string|max:255', 
            'email' => 'required|string|email|max:255', 
            // 'password' => 'required|string|min:6|confirmed',

            // 'nickname' => 'required', 
            'first_name' => 'alpha|string|max:255', 
            'second_name' => 'alpha|string|max:255', 
            'patronymic' => 'alpha|string|max:255', 

            'sex' => 'required', 
            'birthday' => 'date|after:01.01.1940', 

            'phone' => 'string|max:17|required', 
            'extra_phone' => 'string|max:17|nullable', 
            'telegram_id' => 'integer|nullable', 
            'city_id' => 'integer|nullable', 
            'address' => 'string|max:255|nullable', 

            'orgform_status' => 'boolean', 
            'company_name' => 'alpha|string|max:255', 
            // 'inn' => 'required', 
            // 'kpp' => 'required', 
            // 'account_settlement' => 'required', 
            // 'account_correspondent' => 'required', 
            // 'bank' => 'required', 

            // 'passport_number' => 'required', 
            // 'passport_released' => 'required', 
            // 'passport_date' => 'required', 
            // 'passport_address' => 'required', 

            // 'contragent_status' => 'required', 
            // 'lead_id' => 'required', 
            // 'employee_id' => 'required', 
            // 'access_block' => 'required', 
            // 'group_users_id' => 'required', 
            // 'group_filials_id' => 'required', 

        ];
    }
}
