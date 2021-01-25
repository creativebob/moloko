<?php

namespace App;

use App\Models\System\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Carbon\Carbon;

class News extends BaseModel
{
    use Cachable,
        SoftDeletes;

    protected $dates = [
        'deleted_at',
        'publish_begin_date',
        'publish_end_date'
    ];

    protected $fillable = [
        'name',
        'alias',
        'slug',

        'preview',
        'content',

        'photo_id',
        'alt',

        'publish_begin_date',
        'publish_end_date',

        'rubricator_id',
        'rubricators_item_id',

        'display',
        'system',
        'moderation'
    ];

    public function setPublishBeginDateAttribute($value)
    {
        $this->attributes['publish_begin_date'] = Carbon::createFromFormat('d.m.Y', $value);
    }

    public function setPublishEndDateAttribute($value)
    {
        if (isset($value)) {
            $this->attributes['publish_end_date'] = Carbon::createFromFormat('d.m.Y', $value);
        }
    }

    public function photo()
    {
        return $this->belongsTo(Photo::class);
    }

    public function albums()
    {
        return $this->morphToMany(Album::class, 'album_entity');
    }
}
