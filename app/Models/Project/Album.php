<?php

namespace App\Models\Project;

use App\Models\Project\Traits\Publicable;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;

class Album extends Model
{
    use Cachable,
        Publicable;

    // Фотки
    public function photos()
    {
        return $this->morphedByMany('App\Photo', 'album_entity')
            ->where('display', true);
    }

    // Настройки фоток
    public function photo_settings()
    {
        return $this->morphOne('App\PhotoSetting', 'photo_settings');
    }
}
