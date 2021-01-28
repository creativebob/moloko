<?php

namespace App;

use App\Models\System\BaseModel;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\SoftDeletes;

class PhotoSetting extends BaseModel
{
    use Cachable,
        SoftDeletes;

    protected $dates = [
        'deleted_at'
    ];

    protected $fillable = [
        'name',
        'description',

        'photo_settings_id',
        'photo_settings_type',

        'store_format',
        'quality',

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

    public function entity()
    {
        return $this->morphTo(null, 'photo_settings_type', 'photo_settings_id');
    }
}
