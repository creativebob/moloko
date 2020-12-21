<?php

namespace App\Http\Requests\System;

use Illuminate\Foundation\Http\FormRequest;

class OutletUpdateRequest extends FormRequest
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
              'description' => 'nullable|string',

              'stock_id' => 'nullable|integer|exists:stocks,id',
              'template_id' => 'nullable|integer|exists:templates,id',

              'extra_time' => 'nullable|integer',

              'is_main' => 'required|integer|max:1',

              'display' => 'nullable|integer|max:1',
              'system' => 'nullable|integer|max:1',
              'moderation' => 'nullable|integer|max:1',
          ];
        }
  }
