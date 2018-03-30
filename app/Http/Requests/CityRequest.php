<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CityRequest extends FormRequest
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
            'region_name' => 'string|max:255|nullable',
            'region_code' => 'integer|nullable',
            'region_vk_external_id' => 'integer|nullable', 
            'area_name' => 'string|max:255|nullable',
            'city_name' => 'string|max:255|nullable',
            'city_code' => 'integer|nullable',
            'region_id' => 'integer|nullable', 
            'area_id' => 'integer|nullable', 
            'city_vk_external_id' => 'integer', 
            'city_database' => 'integer|nullable',

        ];
    }
}
