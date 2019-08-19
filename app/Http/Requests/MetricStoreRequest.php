<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MetricStoreRequest extends FormRequest
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
//            'name' => 'required|string|max:255',
            'name' => 'string|max:255',
            'description' => 'string|nullable',

//            'property_id' => 'required|integer',
            'property_id' => 'integer',


//            'type' => 'required|string|max:255',
            'type' => 'string|max:255',

            'category_id' => 'integer|nullable',
            'category_entity' => 'string|max:255|nullable',

            'is_required' => 'integer|max:1|nullable',

            'display' => 'integer|max:1|nullable',
            'system' => 'integer|max:1|nullable',
        ];
    }
}
