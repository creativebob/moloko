<?php

namespace App\Http\Requests\System;

use Illuminate\Foundation\Http\FormRequest;

class WorktimeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [

            'mon_begin' => 'regex:[0-1][0-9]|[2][0-3]):[0-5][0-9]|max:5|nullable', 

        ];
    }
}
