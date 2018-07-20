<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GoodsProductRequest extends FormRequest
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
            'company_id' => 'integer|nullable', 
            'name' => 'string|max:255|required', 
            'article' => 'string|max:255', 
            'cost' => 'integer|nullable',
            'avatar' => 'integer|nullable',
            'description' => 'string|nullable', 
            'unit_id' => 'integer|nullable',
            'rule_id' => 'integer|nullable', 
            'goods_category_id' => 'integer|nullable',
            
            'author_id' => 'integer|nullable', 
            'editor_id' => 'integer|nullable', 

            'moderation' => 'integer|max:1|nullable',
            'system_item' => 'integer|max:1|nullable',       
        ];
    }
}
