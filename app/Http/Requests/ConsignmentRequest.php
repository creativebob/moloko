<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConsignmentRequest extends FormRequest
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
        'name' => 'string|max:255|nullable',
        'description' => 'string|nullable',
        'number' => 'integer|nullable',
        'amount' => 'integer|nullable',

        'supplier_id' => 'integer|nullable',

        'draft' => 'integer|max:1|nullable',

        'moderation' => 'integer|max:1|nullable',
        'system_item' => 'integer|max:1|nullable',
        'display' => 'integer|max:1|nullable',
      ];
    }
  }
