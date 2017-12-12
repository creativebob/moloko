<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

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

            'nickname' => 'alpha|string|max:255|nullable', 
            'first_name' => 'alpha|string|max:255|nullable', 
            'second_name' => 'alpha|string|max:255|nullable', 
            'patronymic' => 'alpha|string|max:255|nullable', 

            'sex' => 'required', 
            'birthday' => 'date|after:01.01.1940|nullable', 

            'phone' => 'string|max:17|required', 
            'extra_phone' => 'string|max:17|nullable', 
            'telegram_id' => 'integer|nullable', 
            'city_id' => 'integer|nullable', 
            'address' => 'string|max:255|nullable', 

            'orgform_status' => 'boolean|nullable', 

            // Обязательное поле "Имя компании" если указан статус юридического лица
            'company_name' => 'alpha|string|max:255|required_if:orgform_status, 1|nullable', 

            'inn' => 'max:12|nullable', 

            'bank' => 'required_if:orgform_status,1|nullable', 

            'passport_number' => 'string|max:13|nullable', 
            'passport_released' => 'string|max:255|nullable', 
            'passport_date' => 'date|after:01.01.1970|before:today|nullable', 
            'passport_address' => 'string|max:255|nullable', 

            'contragent_status' => 'required', 

            // 'lead_id' => 'required', 
            // 'employee_id' => 'required', 

            'access_block' => 'boolean|nullable', 
            'group_action_id' => 'integer|required', 
            'group_locality_id' => 'integer|required', 

        ];
    }

    protected function formatErrors(Validator $validator)
    {
        return $validator->errors()->all();
    }

    public function messages()
    {
        return [
            'first_name.required' => 'Ебать, как звать, еблан!',
        ];
    }



}
