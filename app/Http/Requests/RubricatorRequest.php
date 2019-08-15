<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RubricatorRequest extends FormRequest
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
        'name' => 'required|string|max:255',
        'alias' => 'string|max:255|nullable',
        'slug' => 'string|max:255|nullable',

        'photo_id' => 'integer|nullable',
        'description' => 'string|nullable',
        // 'seo_description' => 'string|nullable',

        'display' => 'integer|max:1|nullable',
        'moderation' => 'integer|max:1|nullable',
        'system' => 'integer|max:1|nullable',
      ];
    }
  }
