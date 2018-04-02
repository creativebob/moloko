<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DepartmentRequest extends FormRequest
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
      'department_name' => 'string|max:255|nullable',
      'address' => 'string|max:255|nullable',
      'phone' => 'string|max:17|nullable',  
      'filial_id' => 'integer|nullable',
      'department_parent_id' => 'integer|nullable',

      'city_id' => 'integer|nullable',
      'city_name' => 'string|max:255',

      'first_item' => 'integer|max:1|nullable',
      'medium_item' => 'integer|max:1|nullable',

      'moderation' => 'integer|max:1|nullable',
      'system_item' => 'integer|max:1|nullable',   
    ];
  }
}
