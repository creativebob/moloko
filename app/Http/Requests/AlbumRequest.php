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
        'name' => 'required|string|max:255',
        'category_id' => 'integer|required',

        'personal' => 'integer|max:1|nullable',
        'slug' => 'string|max:255|nullable',

        'description' => 'string|max:255|nullable',
        'delay' => 'integer|max:60|nullable',

        'display' => 'integer|max:1|nullable',
        'moderation' => 'integer|max:1|nullable',
        'system' => 'integer|max:1|nullable',
      ];
    }
  }
