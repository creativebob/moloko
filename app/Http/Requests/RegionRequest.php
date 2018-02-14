<?php

namespace App\Http\Requests;

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
            'region_name' => 'string|max:255',
            'region_code' => 'integer|max:4|nullable',
            'region_vk_external_id' => 'integer|nullable', 
            'region_database' => 'integer|nullable', 
        ];
    }
}
