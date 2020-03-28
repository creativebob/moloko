<?php

namespace App\Http\Requests\System;

use Illuminate\Foundation\Http\FormRequest;

class ToolRequest extends FormRequest
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
        'mode' => 'string|nullable',
        'set_status' => 'string|nullable',
        'tools_product_name' => 'string|nullable',
        'price' => 'integer|nullable',
        'unit_id' => 'integer|nullable',

        'quickly' => 'integer|nullable',

        'category_id' => 'integer|nullable',
        'tools_type_id' => 'integer|nullable',

        'display' => 'nullable|integer|max:1',
            'system' => 'nullable|integer|max:1',
            'moderation' => 'nullable|integer|max:1',
      ];
    }
  }
