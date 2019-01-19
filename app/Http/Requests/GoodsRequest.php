<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GoodsRequest extends FormRequest
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
        'goods_product_name' => 'string|nullable',
        'price' => 'integer|nullable',
        'unit_id' => 'integer|nullable',

        'quickly' => 'integer|nullable',

        'goods_category_id' => 'integer|nullable',
        'goods_type_id' => 'integer|nullable',

        'metrics.*'  => 'integer|distinct',
        'compositions.*'  => 'integer|distinct',

        'display' => 'integer|max:1|nullable',
        'moderation' => 'integer|max:1|nullable',
        'system_item' => 'integer|max:1|nullable',
      ];
    }
  }
