<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConsignmentUpdateRequest extends FormRequest
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
                'amount' => 'integer',

              'name' => 'string|max:255|nullable',
              'description' => 'string|nullable',

                'supplier_id' => 'integer|required',
              'stock_id' => 'integer|required',

                'draft' => 'integer|max:1',

                'moderation' => 'integer|max:1',
                'system' => 'integer|max:1',
                'display' => 'integer|max:1',
          ];
        }
  }
