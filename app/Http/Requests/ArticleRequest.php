<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArticleRequest extends FormRequest
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
            'category_id' => 'string|nullable',
            'unit_id' => 'string|nullable',
            'extra_unit_id' => 'string|nullable',

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

            'portion_status' => 'integer|nullable',
            'portion_name' => 'string|nullable',
            'portion_abbreviation' => 'string|nullable',
            'portion_count' => 'integer|nullable',

            'draft' => 'integer|nullable',
            'width' => 'integer|nullable',

        ];
    }
}
