<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AlbumRequest extends FormRequest
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
        'name' => 'string|max:255',
        'albums_category_id' => 'integer|nullable', 
        'access' => 'integer|nullable', 
        'alias' => 'string|max:255',
        'photo_id' => 'integer|nullable', 
        'description' => 'string|max:255',
        'delay' => 'integer|nullable|max:60',

        'author_id' => 'integer|nullable', 
        'editor_id' => 'integer|nullable', 

        'moderation' => 'integer|max:1|nullable',
        'system_item' => 'integer|max:1|nullable', 
      ];
    }
  }
