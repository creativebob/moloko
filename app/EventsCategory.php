<?php

namespace App;

use App\Models\System\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventsCategory extends BaseModel
{
    use SoftDeletes;

    protected $dates = [
        'deleted_at'
    ];

    protected $fillable = [
        'name',
        'description',
        'seo_description',
        'parent_id',
        'photo_id',

        'processes_type_id',

        'display',
        'system',
        'moderation'
    ];

    public function parent()
    {
        return $this->belongsTo(EventsCategory::class);
    }

    public function childs()
    {
        return $this->hasMany(EventsCategory::class, 'parent_id');
    }

    public function events()
    {
        return $this->hasMany(Event::class, 'category_id');
    }

    public function photo()
    {
        return $this->belongsTo(Photo::class);
    }

    public function metrics()
    {
        return $this->morphToMany(Metric::class, 'entity', 'preset_metric');
    }

    public function manufacturers()
    {
        return $this->morphToMany(Manufacturer::class, 'categories_manufacturer');
    }

    public function groups()
    {
        return $this->morphToMany(ProcessesGroup::class, 'entity', 'processes_group_entity');
    }
}
