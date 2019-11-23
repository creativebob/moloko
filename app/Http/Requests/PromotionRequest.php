<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class PromotionRequest extends FormRequest
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

            'name' => 'required|string|max:255',
            'description' => 'string|nullable',

            'begin_date' => 'date|after:01.01.2018',
            'end_date' => 'date|after:01.01.2018|nullable',

            'moderation' => 'integer|max:1',
            'system' => 'integer|max:1',
            'display' => 'integer|max:1',

        ];
    }

    protected function formatErrors(Validator $validator)
    {
        return $validator->errors()->all();
    }

    public function messages()
    {
        return [
            'login.alpha' => 'Логин должен быть английскими буквами.',
            'login.required' => 'Введите логин. Он необходим',
            'login.string' => 'Логин должен быть одним словом (без пробелов).',
            'login.max' => 'Слишком длинный логин.', 

            'alias.alpha' => 'Алиас должен быть английскими буквами.',
            'alias.min' => 'Алиас должен быть не менее 6 символов.',

        ];
    }



}
