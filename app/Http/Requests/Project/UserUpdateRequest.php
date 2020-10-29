<?php

namespace App\Http\Requests\Project;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class UserUpdateRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {

        return [

//            'first_name' => 'string|max:255|nullable',
//            'second_name' => 'string|max:255|nullable',
            'email' => 'nullable|string|email|max:255',
            // 'patronymic' => 'string|max:255|nullable',

            // 'sex' => 'nullable',
            // 'birthday' => 'date|after:01.01.1940|nullable',

            // 'main_phone' => 'string|max:17|required',
            // 'extra_phones.*' => 'string|max:17|nullable',

            // 'telegram_id' => 'integer|nullable',
            // 'city_id' => 'integer|nullable',
            // 'address' => 'string|max:255|nullable',

            // 'orgform_status' => 'boolean|nullable',

            // Обязательное поле "Имя компании" если указан статус юридического лица
            // 'company_name' => 'alpha|string|max:255|required_if:orgform_status, 1|nullable',

        ];
    }

    protected function formatErrors(Validator $validator)
    {
        return $validator->errors()->all();
    }

    public function messages()
    {
        return [
            'first_name.required' => 'Введите имя!',
            'login.required_without' => 'Вы открываете доступ - укажите логин пользователя!',
        ];
    }



}
