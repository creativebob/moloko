<?php

namespace App\Http\Requests;

// Модели
use App\Stage;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

use Illuminate\Http\Request;

class MyStageRequest extends FormRequest
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

    // Массив ошибок
    protected $error = [];

    public function rules(Request $request)
    {

        $stage = Stage::with('fields.rules')->findOrFail($request->stage_id);
        // dd($stage);

        $mass = [];
        $error = [];
        foreach ($stage->fields as $field) {
            $mass[$field->name] = $field->rules->implode('rule', '|');

            foreach ($field->rules as $rule) {
                $error[$field->name.'.'.$rule->name] = $rule->error;
            }
        }

        // Записываем массив ошибок
        $this->error = $error;
        // dd($mass);
        // dd($error);
        return $mass;
    }

    protected function formatErrors(Validator $validator)
    {
        // dd($validator);
        return $validator->errors()->all();
    }

    public function messages()
    {   
        return $this->error;

    }



}
