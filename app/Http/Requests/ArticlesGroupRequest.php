<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArticlesGroupRequest extends FormRequest
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
            'set_status' => 'integer|max:1|nullable',
            'unit_id' => 'integer|nullable',

            'display' => 'integer|max:1|nullable',
            'moderation' => 'integer|max:1|nullable',
            'system' => 'integer|max:1|nullable',
        ];
    }
}
