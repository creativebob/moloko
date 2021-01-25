<?php

namespace App;

use App\Models\System\BaseModel;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class Album extends BaseModel
{
    use Cachable;

    protected $fillable = [
        'name',
        'description',
        'slug',

        'category_id',

        'personal',

        'delay',

        'photo_id',

        'display',
        'system',
        'moderation'
    ];

    // Категория
    public function category()
    {
        return $this->belongsTo(AlbumsCategory::class);
    }

    // Фото
    public function photo()
    {
        return $this->belongsTo(Photo::class);
    }

    // Фотки
    public function photos()
    {
        return $this->morphedByMany(Photo::class, 'album_entity');
    }

    // Настройки фоток
    public function photo_settings()
    {
        return $this->morphOne(PhotoSetting::class, 'photo_settings');
    }
}
