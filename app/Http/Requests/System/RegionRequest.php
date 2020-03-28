<?php

namespace App\Http\Requests\System;

use Illuminate\Foundation\Http\FormRequest;

class RegionRequest extends FormRequest
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
            'code' => 'integer|max:4|nullable',
            'vk_external_id' => 'integer|nullable', 
            'region_database' => 'integer|nullable', 
        ];
    }
}
