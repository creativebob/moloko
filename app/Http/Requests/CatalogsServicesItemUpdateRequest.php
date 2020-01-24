<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CatalogsServicesItemUpdateRequest extends FormRequest
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
        $settings = getSettings('catalogs_services_items');

        return [
            'name' => 'required|string|max:255',
            'description' => 'string|nullable',
            'seo_description' => 'string|nullable',
            'parent_id' => 'integer|nullable',

            'color' => 'string|max:7||nullable',

            'file' => 'max:'.$settings['img_max_size'].'|mimes:'.$settings['img_formats'].'|nullable',

            'display' => 'integer|max:1|nullable',
            'moderation' => 'integer|max:1|nullable',
            'system' => 'integer|max:1|nullable',
        ];
    }
}
