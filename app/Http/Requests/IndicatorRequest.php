<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndicatorRequest extends FormRequest
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
            'description' => 'string|nullable',

            'indicators_category_id' => 'integer|nullable',
            'entity_id' => 'integer|nullable',
            'unit_id' => 'integer|nullable',

            'moderation' => 'integer|max:1|nullable',
            'system' => 'integer|max:1|nullable',
            'display' => 'integer|max:1|nullable',
        ];
    }
}
