<?php

namespace App\Http\Requests\System;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class FlowRequest extends FormRequest
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
            'process_id' => 'required|integer|exists:processes,id',
            'filial_id' => 'required|integer|exists:departments,id',

            'start_date' => 'required|date|date_format:d.m.Y|after:01.01.2018',
            'start_time' => 'required',

            'finish_date' => 'required|date|date_format:d.m.Y|after:01.01.2018',
            'finish_time' => 'required',

            'capacity_min' => 'required|integer',
            'capacity_max' => 'required|integer',

            'display' => 'nullable|integer|max:1',
            'system' => 'nullable|integer|max:1',
            'moderation' => 'nullable|integer|max:1',
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
