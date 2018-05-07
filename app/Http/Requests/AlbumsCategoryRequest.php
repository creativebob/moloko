<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AlbumsCategoryRequest extends FormRequest
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
        'category_db' => 'integer|nullable',
        'albums_category_db' => 'integer|nullable', 
        'albums_category_name' => 'string|max:255',
        'albums_category_id' => 'integer|nullable', 
        'category_id' => 'integer|nullable', 

        'moderation' => 'integer|max:1|nullable',
        'system_item' => 'integer|max:1|nullable', 
      ];
    }
  }
