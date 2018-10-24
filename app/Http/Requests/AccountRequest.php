<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class AccountRequest extends FormRequest
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

            'name' => 'string|max:255', 

            'login' => 'required|string|max:255',
            'alias' => 'alpha|string|min:6|max:255|nullable',
            'password[]' => 'sometimes|string|min:6|confirmed|nullable',

            'moderation' => 'integer|max:1|nullable',
            'system_item' => 'integer|max:1|nullable', 

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
