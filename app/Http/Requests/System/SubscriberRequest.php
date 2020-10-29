<?php

namespace App\Http\Requests\System;

use Illuminate\Foundation\Http\FormRequest;

class SubscriberRequest extends FormRequest
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
              'name' => 'nullable|string|max:255',
              'email' => 'required|string|email|max:255',

              'is_active' => 'nullable|integer|max:1',
              'deny' => 'nullable|integer|max:1',

              'display' => 'nullable|integer|max:1',
              'system' => 'nullable|integer|max:1',
              'moderation' => 'nullable|integer|max:1',
          ];
        }
  }
