<?php

namespace App\Http\Requests\System;

use Illuminate\Foundation\Http\FormRequest;

class WorkplaceUpdateRequest extends FormRequest
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

//              'filial_id' => 'required|integer|exists:departments,id',
//              'outlet_id' => 'nullable|integer|exists:outlets,id',
              'ip' => 'nullable'
//              'ip' => 'nullable|ip'
          ];
        }
  }
