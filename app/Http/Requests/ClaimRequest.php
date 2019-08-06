<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClaimRequest extends FormRequest
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
        'body' => 'string|max:255|required', 
        'case_number' => 'string|nullable',
        'serial_number' => 'string|nullable',
        'lead_id' => 'integer|nullable',
        'manager_id' => 'integer|nullable',
        'status' => 'integer|nullable',

        'moderation' => 'integer|max:1|nullable',    
        'sort' => 'integer|nullable',
        'display' => 'integer|max:1|nullable',
        'system' => 'integer|max:1|nullable',          
      ];
    }
  }
