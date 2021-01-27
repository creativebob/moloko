<?php

namespace App;

use App\Models\System\BaseModel;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class PhotoSetting extends BaseModel
{
    use Cachable;

    protected $dates = [
        'deleted_at'
    ];

    protected $fillable = [
        'name',
        'description',

        'setting_id',
        'setting_type',

        'img_min_width',
        'img_min_height',

        'img_small_width',
        'img_small_height',

        'img_medium_width',
        'img_medium_height',

        'img_large_width',
        'img_large_height',

        'img_formats',
        'img_max_size',

        'strict_mode',
        'crop_mode',

        'display',
        'system',
        'moderation'
    ];

    public function settingable()
    {
        return $this->morphTo();
    }

}
