<?php

namespace App\Http\Requests\System;

use Illuminate\Foundation\Http\FormRequest;

class WorkflowStoreRequest extends FormRequest
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
              'category_id' => 'required|integer',
              'processes_type_id' => 'required|integer|exists:processes_types,id',
              'quickly' => 'integer|max:1',
              'kit' => 'integer|max:1',
          ];
        }
  }
