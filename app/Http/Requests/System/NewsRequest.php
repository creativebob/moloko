<?php

namespace App\Http\Requests\System;

use Illuminate\Foundation\Http\FormRequest;

class NewsRequest extends FormRequest
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
            'preview' => 'nullable|string',
            'content' => 'nullable|string',

            'alias' => 'nullable|string',
            'slug' => 'nullable|string',
            'alt' => 'nullable|string',

            'publish_begin_date' => 'required|date|after:01.01.2018',
            'publish_end_date' => 'nullable|date|after:01.01.2018',

            'rubricator_id' => 'required|integer|exists:rubricators,id',
            'rubricators_item_id' => 'required|integer|exists:rubricators_items,id',

            'display' => 'nullable|integer|max:1',
            'system' => 'nullable|integer|max:1',
            'moderation' => 'nullable|integer|max:1',
        ];
    }
}
