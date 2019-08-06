<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RubricatorsItemRequest extends FormRequest
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
        'rubricator_id' => 'integer|nullable',

        'parent_id' => 'integer|nullable',
        'category_id' => 'integer|nullable',

        // 'photo_id' => 'integer|nullable',
        // 'description' => 'string|nullable',
        // 'seo_description' => 'string|nullable',

        'display' => 'integer|max:1|nullable',
        'moderation' => 'integer|max:1|nullable',
        'system' => 'integer|max:1|nullable',
      ];
    }
  }
