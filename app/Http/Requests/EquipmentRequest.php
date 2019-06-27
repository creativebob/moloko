<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EquipmentRequest extends FormRequest
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
        'mode' => 'string|nullable',
        'set_status' => 'string|nullable',
        'equipments_product_name' => 'string|nullable',
        'price' => 'integer|nullable',
        'unit_id' => 'integer|nullable',

        'quickly' => 'integer|nullable',

        'category_id' => 'integer|nullable',
        'equipments_type_id' => 'integer|nullable',

        'display' => 'integer|max:1|nullable',
        'moderation' => 'integer|max:1|nullable',
        'system_item' => 'integer|max:1|nullable',
      ];
    }
  }
