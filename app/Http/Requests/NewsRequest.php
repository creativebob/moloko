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
        'name' => 'string|max:255',
        'title' => 'string|max:255',
        'preview' => 'string',
        'content' => 'string|nullable',
        'alias' => 'string|nullable',

        'publish_begin_date' => 'date|after:01.01.2018|nullable',
        'publish_end_date' => 'date|after:01.01.2018|nullable',

        'display' => 'integer|nullable',
        'moderation' => 'integer|max:1|nullable',
        'system_item' => 'integer|max:1|nullable',
      ];
    }
  }
