<?php

namespace App\Http\Requests\System;

use Illuminate\Foundation\Http\FormRequest;

class PhotoSettingRequest extends FormRequest
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
              'name' => 'required|string|max:255',
              'description' => 'nullable|string',

              'store_format' => 'required|string',
              'quality' => 'required|integer',

              'img_min_width' => 'required|integer',
              'img_min_height' => 'required|integer',

              'img_small_width' => 'required|integer',
              'img_small_height' => 'required|integer',

              'img_medium_width' => 'required|integer',
              'img_medium_height' => 'required|integer',

              'img_large_width' => 'required|integer',
              'img_large_height' => 'required|integer',

              'img_formats' => 'required|string',
              'img_max_size' => 'required|integer',

              'strict_mode' => 'required|integer',
              'crop_mode' => 'required|integer',

              'display' => 'nullable|integer|max:1',
              'system' => 'nullable|integer|max:1',
              'moderation' => 'nullable|integer|max:1',
          ];
        }
  }
