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
        'name' => 'string|max:255|required',
        'albums_category_id' => 'integer',

        'personal' => 'integer|max:1|nullable',
        'alias' => 'string|max:255|nullable',
        'description' => 'string|max:255|nullable',
        'delay' => 'integer|max:60|nullable',

        'display' => 'integer|max:1|nullable',
        'moderation' => 'integer|max:1|nullable',
        'system_item' => 'integer|max:1|nullable',
      ];
    }
  }
