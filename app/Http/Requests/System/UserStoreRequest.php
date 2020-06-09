<?php

namespace App\Http\Requests\System;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class UserStoreRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {

        return [

            'login' => 'sometimes|required|string|max:255',
            'login' => 'required_without:access_block',

            'email' => 'nullable|string|email|max:255',


            // 'password' => 'required|string|min:6|confirmed',

            'nickname' => 'alpha|string|max:255|nullable',
            'first_name' => 'string|max:255|nullable',
            'second_name' => 'string|max:255|nullable',
            'patronymic' => 'string|max:255|nullable',

            // 'sex' => 'nullable',
            'birthday_date' => 'date|after:01.01.1940|nullable',

            // 'main_phone' => 'string|max:17|required',
            // 'extra_phones.*' => 'string|max:17|nullable',

            'telegram_id' => 'integer|nullable',
            'city_id' => 'integer|nullable',
            'address' => 'string|max:255|nullable',

            'orgform_status' => 'boolean|nullable',

            // Обязательное поле "Имя компании" если указан статус юридического лица
            'company_name' => 'alpha|string|max:255|required_if:orgform_status, 1|nullable',

            'inn' => 'max:12|nullable',

            'bank' => 'required_if:orgform_status,1|nullable',

            'passport_number' => 'unique:users|string|max:13|nullable',
            'passport_released' => 'string|max:255|nullable',
            'passport_date' => 'date|after:01.01.1970|before:today|nullable',
            'passport_address' => 'string|max:255|nullable',

            'degree' => 'string|max:200|nullable',
            'specialty' => 'string|max:200|nullable',
            'quote' => 'string|max:500|nullable',

            // 'user_type' => 'required',
            // 'access_block' => 'boolean|nullable',

            'is_vip' => 'integer|max:1',

            'moderation' => 'integer|max:1|nullable',
            'system' => 'integer|max:1|nullable',

        ];
    }

    protected function formatErrors(Validator $validator)
    {
        return $validator->errors()->all();
    }

    public function messages()
    {
        return [
            'first_name.required' => 'Напишите имя пользователя',
            'login.required_without' => 'Вы открываете доступ - укажите логин пользователя!',
        ];
    }



}
