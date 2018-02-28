<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SectorRequest extends FormRequest
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
              'category_db' => 'integer|nullable',
              'sector_db' => 'integer|nullable', 
              'sector_name' => 'string|max:255',
              'sector_id' => 'integer|nullable', 
              'category_id' => 'integer|nullable', 
        ];
    }
}
