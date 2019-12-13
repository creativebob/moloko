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
            'name' => 'required|string|max:255',
            'alias' => 'nullable|string|max:255',
            'description' => 'nullable|string',

            'type' => 'required|string|max:20',

            'property_id' => 'required|integer',
            'entity_id' => 'required|integer',

            'is_required' => 'nullable|boolean',

            'list_type' => 'nullable|string|max:255',


            'decimal_place' => 'nullable|integer|max:1',
            'min' => 'nullable|integer',
            'max' => 'nullable|integer',
            'unit_id' => 'nullable|integer',


        ];
    }
}
