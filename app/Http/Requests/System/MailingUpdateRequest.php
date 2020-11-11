<?php

namespace App\Http\Requests\System;

use Illuminate\Foundation\Http\FormRequest;

class MailingUpdateRequest extends FormRequest
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
              'description' => 'nullable|string',

              'subject' => 'nullable|string|max:255',
              'from_name' => 'nullable|string|max:255',
              'from_email' => 'nullable|string|max:255',

              'is_active' => 'nullable|integer|max:1',

              'template_id' => 'nullable|integer|exists:templates,id',
              'mailing_list_id' => 'nullable|integer|exists:mailing_lists,id',

              'started_at' => 'nullable|date|date_format:d.m.Y|after:01.01.2018',

              'display' => 'nullable|integer|max:1',
              'system' => 'nullable|integer|max:1',
              'moderation' => 'nullable|integer|max:1',
          ];
        }
  }
