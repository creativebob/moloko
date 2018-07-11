<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ServicesCategoryRequest extends FormRequest
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
        'first_item' => 'integer|max:1|nullable',
        'medium_item' => 'integer|max:1|nullable',
        'services_category_id' => 'integer|nullable', 
        'parent_id' => 'integer|nullable', 
       
        'category_id' => 'integer|nullable', 

        'photo_id' => 'integer|nullable',

        'description' => 'string|nullable',
        'seo_description' => 'string|nullable',

        'moderation' => 'integer|max:1|nullable',
        'system_item' => 'integer|max:1|nullable', 
      ];
    }
  }
