<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NavigationRequest extends FormRequest
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
        'site_id' => 'integer|required',
        'first_item' => 'integer',

        'moderation' => 'integer|max:1|nullable',
        'system_item' => 'integer|max:1|nullable',          
      ];
    }
  }
