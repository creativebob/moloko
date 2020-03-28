<?php

namespace App\Http\Requests\System;

use Illuminate\Foundation\Http\FormRequest;

class ProcessRequest extends FormRequest
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
            'category_id' => 'integer|nullable',
            'unit_id' => 'integer|nullable',
            'unit_length_id' => 'integer|nullable',
            'extra_unit_id' => 'integer|nullable',

            'name' => 'required|string|max:255',
            'description' => 'string|nullable',

            'internal' => 'string|nullable',
            'manually' => 'string|nullable',
            'external' => 'string|nullable',

            // 'manufacturer_id' => 'integer|required',
            // 'articles_group_id' => 'integer|required',

            'cost_default' => 'integer|nullable',
            'cost_mode' => 'integer|nullable',
            'price_default' => 'integer|nullable',
            'price_mode' => 'integer|nullable',
            'price_rule_id' => 'integer|nullable',

            'draft' => 'integer|nullable',
            'length' => 'integer|nullable',
            'set' => 'integer|nullable',

        ];
    }
}
