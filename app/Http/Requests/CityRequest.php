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
            'city_name' => 'string|nullable',
            'code' => 'integer|nullable',
            'vk_external_id' => 'integer|nullable',
            'area_name' => 'string|max:255|nullable',
            'region_name' => 'string|max:255|nullable',

            'checkbox' => 'integer|nullable',
            'city_db' => 'integer|nullable',

        ];
    }
}
