<?php

namespace App\Http\Requests\System;

use Illuminate\Foundation\Http\FormRequest;

class CampaignStoreRequest extends FormRequest
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

            'end_date' => 'nullable|string',
            'end_time' => 'nullable|string',

            // 'is_block' => 'integer|max:1',

            'display' => 'nullable|integer|max:1',
            'moderation' => 'nullable|integer|max:1',
            'system' => 'nullable|integer|max:1',
        ];
    }
}
