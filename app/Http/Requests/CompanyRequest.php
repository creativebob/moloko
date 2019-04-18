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

            'name' => 'string|max:255|required', 
            // 'alias' => 'string|max:255|alpha|unique:companies', 
             'alias' => 'string|max:255|alpha_dash|nullable',

            'main_phone' => 'string|max:17|required',
            'extra_phones.*' => 'string|max:17|nullable',

            // 'extra_phone' => 'string|max:17|nullable', 
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
    }
}