<?php

namespace App\Http\Requests;

use App\Http\Controllers\Traits\Photable;
use Illuminate\Foundation\Http\FormRequest;

class WorkflowUpdateRequest extends FormRequest
{

    public function __construct()
    {
        $this->settings = $this->getPhotoSettings('raws');
    }

    use Photable;
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

              'display' => 'integer|max:1|nullable',
              'moderation' => 'integer|max:1|nullable',
              'system' => 'integer|max:1|nullable',

              'photo' => 'nullable|mimes:'.str_replace('.', '', $this->settings['img_formats']).'|dimensions:min_width='.$this->settings['img_min_width'].',min_height='.$this->settings['img_min_height']
          ];
    }

    public function messages()
    {
        return [
            'photo.dimensions' => 'Фото должно быть не менее '.$this->settings['img_min_width'].' x '.$this->settings['img_min_height'].' px',
        ];
    }
}
