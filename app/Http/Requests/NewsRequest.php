<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NewsRequest extends FormRequest
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
        'site_id' => 'integer|nullable',
        'title' => 'string|max:255', 
        'preview' => 'string', 
        'photo_id' => 'integer|nullable',
        'content' => 'string|nullable', 
        'alias' => 'string|nullable', 

        'date_publish_begin' => 'date|after:01.01.2018|nullable',
        'date_publish_end' => 'date|after:01.01.2018|nullable',

        'display' => 'integer|nullable',

        'author_id' => 'integer|nullable',
        'editor_id' => 'integer|nullable',
        
        'moderation' => 'integer|max:1|nullable',
        'system_item' => 'integer|max:1|nullable',          
      ];
    }
  }
