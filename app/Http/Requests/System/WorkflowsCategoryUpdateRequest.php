<?php

namespace App\Http\Requests\System;

use Illuminate\Foundation\Http\FormRequest;

class WorkflowsCategoryUpdateRequest extends FormRequest
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
        $settings = getPhotoSettings('workflows_categories');

        return [
            'name' => 'required|string|max:255',
            'description' => 'string|nullable',
            'seo_description' => 'string|nullable',
            'parent_id' => 'integer|nullable',

            'processes_type_id' => 'required|integer|max:1',

            'photo' => "nullable|max:{$settings['img_max_size']}|mimes:{$settings['img_formats']}",

            'manufacturers.*' => 'integer|nullable',

            'display' => 'nullable|integer|max:1',
            'system' => 'nullable|integer|max:1',
            'moderation' => 'nullable|integer|max:1',
        ];
    }
}
