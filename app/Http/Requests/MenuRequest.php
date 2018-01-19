<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MenuRequest extends FormRequest
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
            'menu_name' => 'string|max:255|nullable', 
            'menu_icon' => 'string|max:255|nullable',
            'navigation_id' => 'integer', 
            'menu_parent_id' => 'integer|nullable', 
            'page_id' => 'integer|nullable',             
        ];
    }
}
