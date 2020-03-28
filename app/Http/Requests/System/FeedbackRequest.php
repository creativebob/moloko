<?php

namespace App\Http\Requests\System;

use Illuminate\Foundation\Http\FormRequest;

class FeedbackRequest extends FormRequest
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
      'person' => 'string|max:255|required',
      'job' => 'string|max:255|required',
      'body' => 'string|nullable',
      'call_date' => 'date|after:01.01.2010|nullable', 

      'moderation' => 'integer|max:1|nullable',
      'system' => 'integer|max:1|nullable',   
    ];
  }
}
