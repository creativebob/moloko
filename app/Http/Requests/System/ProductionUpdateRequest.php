<?php

namespace App\Http\Requests\System;

use Illuminate\Foundation\Http\FormRequest;

class ProductionUpdateRequest extends FormRequest
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

                'number' => 'integer|nullable',

              'name' => 'string|max:255|nullable',
              'description' => 'string|nullable',
	          
              'stock_id' => 'integer|required',

                'draft' => 'integer|max:1',

                'moderation' => 'integer|max:1',
                'system' => 'integer|max:1',
                'display' => 'integer|max:1',
          ];
        }
  }
