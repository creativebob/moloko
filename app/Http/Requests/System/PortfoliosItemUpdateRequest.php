<?php

namespace App\Http\Requests\System;

use Illuminate\Foundation\Http\FormRequest;

class PortfoliosItemUpdateRequest extends FormRequest
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
        $settings = getPhotoSettings('portfolios_items');

        return [
            'portfolio_id',

            'name' => 'required|string|max:255',

            'title' => 'nullable|string',

            'description' => 'nullable|string',
            'seo_description' => 'nullable|string',
            'parent_id' => 'nullable|integer',

            'photo' => "nullable|max:{$settings['img_max_size']}|mimes:{$settings['img_formats']}",
            'color' => 'nullable|string|max:7',

            'display_mode_id' => 'nullable|integer',
            'directive_category_id' => 'nullable|integer',

            'is_controllable_mode' => 'nullable|integer|max:1',
            'is_show_subcategory' => 'nullable|integer|max:1',

            'display' => 'nullable|integer|max:1',
            'system' => 'nullable|integer|max:1',
            'moderation' => 'nullable|integer|max:1',
        ];
    }
}
