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
      'city_id' => 'integer|nullable', 
      'filial_db' => 'integer|nullable',
      'department_db' => 'integer|nullable', 
      'filial_name' => 'string|max:255|nullable',
      'department_name' => 'string|max:255|nullable',
      'filial_address' => 'string|max:255|nullable',
      'department_address' => 'string|max:255|nullable',
      'filial_phone' => 'string|max:17|nullable',
      'department_phone' => 'string|max:17|nullable', 
      'filial_id' => 'integer|nullable',  
      'department_id' => 'integer|nullable', 
    ];
  }
}
