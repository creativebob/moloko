<?php

namespace App;

use App\Models\System\BaseModel;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class Photo extends BaseModel
{
    use Cachable;

    protected $fillable = [
        'name',
        'title',
        'description',

        'path',
        'link',
        'color',

        'width',
        'height',
        'size',
        'extension',

        'access',
        'album_id',

        'display',
        'system',
        'moderation'
    ];


    public function cur_news()
    {
        return $this->hasOne(News::class);
    }

    public function avatar()
    {
        return $this->hasOne(Album::class);
    }

    public function album()
    {
        return $this->belongsTo(Album::class);
    }

    public function albums()
    {
        return $this->morphToMany(Album::class, 'album_entity');
    }

    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function goods()
    {
        return $this->hasOne(Goods::class);
    }

    public function raw()
    {
        return $this->hasOne(Raw::class);
    }
}
