<?php

namespace App\Http\Requests\System;

use Illuminate\Foundation\Http\FormRequest;

class OutcomesCategoryUpdateRequest extends FormRequest
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
        $settings = getPhotoSettings('outcomes_categories');

        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'seo_description' => 'nullable|string',
            'parent_id' => 'nullable|integer',

            'file' => 'max:' . $settings['img_max_size'].'|mimes:' . $settings['img_formats'].'|nullable',

            'display' => 'nullable|integer|max:1',
            'system' => 'nullable|integer|max:1',
            'moderation' => 'nullable|integer|max:1',
        ];
    }
}
