<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CatalogsGoodsItemStoreRequest extends FormRequest
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
            'parent_id' => 'integer|nullable',

            'catalogs_goods_id' => 'integer|exists:catalogs_goods',

            'display' => 'integer|max:1|nullable',
            'system' => 'integer|max:1|nullable',
        ];
    }
}
