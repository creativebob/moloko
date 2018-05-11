<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PhotoRequest extends FormRequest
{
  public function authorize()
  {
    return false;
  }

  public function rules()
  {
    return [
      // 'photo' => 'file',
      'company_id' => 'integer|nullable',
      'name' => 'string|max:255|nullable',
      'title' => 'string|max:255|nullable',
      'description' => 'string|nullable',
      'path' => 'string|nullable',
      'alias' => 'string|nullable',
      'width' => 'integer|nullable',
      'height' => 'integer|nullable',
      // 'size' => 'integer|nullable',
      'extension' => 'string|nullable',
      'photo_access' => 'integer|nullable',

      'author_id' => 'integer|nullable', 
      'editor_id' => 'integer|nullable', 

      'moderation' => 'integer|max:1|nullable',
      'system_item' => 'integer|max:1|nullable', 

    ];
  }
}
