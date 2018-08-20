<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CatalogRequest extends FormRequest
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
        'name' => 'string|max:255|required', 
        'site_id' => 'integer|nullable',
        'first_item' => 'integer|nullable',
        'medium_item' => 'integer|nullable',
        'moderation' => 'integer|max:1|nullable',
        'display' => 'integer|max:1|nullable',
        'parent_id' => 'integer|nullable',
        'system_item' => 'integer|max:1|nullable',          
      ];
    }
  }
