<?php

namespace App;

use App\Models\System\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;


class AlbumsCategory extends BaseModel
{

    use Cachable,
        SoftDeletes;

    protected $dates = [
        'deleted_at'
    ];

    protected $fillable = [
        'name',
        'description',
        'seo_description',
        'parent_id',
        'photo_id',

        'display',
        'system',
        'moderation'
    ];

    // Родитель
    public function parent()
    {
        return $this->belongsTo(AlbumsCategory::class);
    }

    // Вложенные
    public function childs()
    {
        return $this->hasMany(AlbumsCategory::class, 'parent_id');
    }

    // Альбомы
    public function albums()
    {
    	return $this->hasMany(Album::class, 'category_id');
    }

}
