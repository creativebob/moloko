<?php

namespace App\Http\Requests;

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
        $settings = getPhotoSettings('raws_categories');

        return [
            'name' => 'required|string|max:255',
            'description' => 'string|nullable',
            'seo_description' => 'string|nullable',
            'parent_id' => 'integer|nullable',

            'processes_type_id' => 'required|integer|max:1',

            'file' => 'max:'.$settings['img_max_size'].'|mimes:'.$settings['img_formats'].'|nullable',

            'manufacturers.*' => 'integer|nullable',

            'display' => 'integer|max:1|nullable',
            'moderation' => 'integer|max:1|nullable',
            'system' => 'integer|max:1|nullable',
        ];
    }
}
