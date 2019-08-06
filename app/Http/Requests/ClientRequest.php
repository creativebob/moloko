<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class ClientRequest extends FormRequest
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


            // Валидация юзера

            'login' => 'required|string|max:255', 
            'email' => 'nullable|string|email|max:255', 
            // 'password' => 'required|string|min:6|confirmed',

            'nickname' => 'alpha|string|max:255|nullable', 
            'first_name' => 'string|max:255|nullable', 
            'second_name' => 'string|max:255|nullable', 
            'patronymic' => 'string|max:255|nullable', 

            // 'sex' => 'nullable', 
            'birthday' => 'date|after:01.01.1940|nullable', 

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

            'access_block' => 'boolean|nullable', 

            'moderation' => 'integer|max:1|nullable',
            'system' => 'integer|max:1|nullable', 


            'name' => 'string|max:255|required', 
            // 'alias' => 'string|max:255|alpha|unique:companies', 
             'alias' => 'string|max:255|alpha',

            'main_phone' => 'string|max:17|required',
            'extra_phones.*' => 'string|max:17|nullable',

            'account_settlement' => 'string|nullable', 
            'account_correspondent' => 'string|nullable', 
            'bank' => 'string|max:255|nullable', 
            'inn' => 'max:12|nullable', 
            'kpp' => 'max:255|nullable',

            // Валидация полей времени в расписании

            'mon_begin' => 
            array(
                'required_with:mon_end',
                'max:5',
                'nullable',
                'regex:/([0-1][0-9]|[2][0-3]):([0-5][0-9])/u'
            ), 

            'mon_end' => 
            array(
                'required_with:mon_begin',
                'max:5',
                'nullable',
                'regex:/([0-1][0-9]|[2][0-3]):([0-5][0-9])/u'
            ), 

            'tue_begin' => 
            array(
                'required_with:tue_end',
                'max:5',
                'nullable',
                'regex:/([0-1][0-9]|[2][0-3]):([0-5][0-9])/u'
            ), 

            'tue_end' => 
            array(
                'required_with:tue_begin',
                'max:5',
                'nullable',
                'regex:/([0-1][0-9]|[2][0-3]):([0-5][0-9])/u'
            ), 

            'wed_begin' => 
            array(
                'required_with:wed_end',
                'max:5',
                'nullable',
                'regex:/([0-1][0-9]|[2][0-3]):([0-5][0-9])/u'
            ), 

            'wed_end' => 
            array(
                'required_with:wed_begin',
                'max:5',
                'nullable',
                'regex:/([0-1][0-9]|[2][0-3]):([0-5][0-9])/u'
            ), 
            'thu_begin' => 
            array(
                'required_with:thu_end',
                'max:5',
                'nullable',
                'regex:/([0-1][0-9]|[2][0-3]):([0-5][0-9])/u'
            ), 

            'thu_end' => 
            array(
                'required_with:thu_begin',
                'max:5',
                'nullable',
                'regex:/([0-1][0-9]|[2][0-3]):([0-5][0-9])/u'
            ), 

            'fri_begin' => 
            array(
                'required_with:fri_end',
                'max:5',
                'nullable',
                'regex:/([0-1][0-9]|[2][0-3]):([0-5][0-9])/u'
            ), 

            'fri_end' => 
            array(
                'required_with:fri_begin',
                'max:5',
                'nullable',
                'regex:/([0-1][0-9]|[2][0-3]):([0-5][0-9])/u'
            ), 

            'sat_begin' => 
            array(
                'required_with:sat_end',
                'max:5',
                'nullable',
                'regex:/([0-1][0-9]|[2][0-3]):([0-5][0-9])/u'
            ), 

            'sat_end' => 
            array(
                'required_with:sat_begin',
                'max:5',
                'nullable',
                'regex:/([0-1][0-9]|[2][0-3]):([0-5][0-9])/u'
            ), 

            'sun_begin' => 
            array(
                'required_with:sun_end',
                'max:5',
                'nullable',
                'regex:/([0-1][0-9]|[2][0-3]):([0-5][0-9])/u'
            ), 

            'sun_end' => 
            array(
                'required_with:sun_begin',
                'max:5',
                'nullable',
                'regex:/([0-1][0-9]|[2][0-3]):([0-5][0-9])/u'
            ),  
        ];




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
        ];
    }



}
