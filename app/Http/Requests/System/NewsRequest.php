<?php

namespace App\Http\Requests\System;

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
        'preview' => 'string|nullable',
        'content' => 'string|nullable',
        'alias' => 'string|nullable',
        'slug' => 'string|nullable',

        'publish_begin_date' => 'date|after:01.01.2018',
        'publish_end_date' => 'date|after:01.01.2018|nullable',

        'rubricator_id' => 'integer|nullable',
        'rubricators_item_id' => 'integer|nullable',

        'display' => 'nullable|integer|max:1',
            'system' => 'nullable|integer|max:1',
            'moderation' => 'nullable|integer|max:1',
      ];
    }
  }
