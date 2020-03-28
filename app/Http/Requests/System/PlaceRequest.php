<?php

namespace App\Http\Requests\System;

use Illuminate\Foundation\Http\FormRequest;

class PlaceRequest extends FormRequest
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
      'description' => 'string|nullable',
      'phone' => 'string|max:17|nullable', 

      'square' => 'integer|nullable|required',
      'stockroom_status' => 'integer|nullable',
      'rent_status' => 'integer|nullable',

      'moderation' => 'integer|max:1|nullable',
      'system' => 'integer|max:1|nullable',
    ];
  }
}
