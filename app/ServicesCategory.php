<?php

namespace App;

use App\Models\System\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServicesCategory extends BaseModel
{
    //    use Cachable;
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

        'is_direction',

        'display',
        'system',
        'moderation'
    ];

    // Родитель
    public function parent()
    {
        return $this->belongsTo(ServicesCategory::class);
    }

    // Вложенные
    public function childs()
    {
        return $this->hasMany(ServicesCategory::class, 'parent_id');
    }

    // Услуги
    public function services()
    {
        return $this->hasMany(Service::class, 'category_id');
    }

    // Аватар
    public function photo()
    {
        return $this->belongsTo(Photo::class);
    }

    // Процессы
    public function prcesses()
    {
        return $this->belongsToMany(Process::class, 'services')
        ->where('draft', false)
        ->where('services.archive', false);
    }

    // Метрики
    public function metrics()
    {
        return $this->morphToMany(Metric::class, 'entity', 'preset_metric');
    }

    public function workflows()
    {
        return $this->belongsToMany(Workflow::class, 'preset_workflow');
    }

    // Производители
    public function manufacturers()
    {
        return $this->morphToMany(Manufacturer::class, 'categories_manufacturer');
    }

    public function groups()
    {
        return $this->morphToMany(ProcessesGroup::class, 'entity', 'processes_group_entity');
    }

    // Направление
    public function direction()
    {
        return $this->morphOne(Direction::class, 'category');
    }

    public function getNameWithParentAttribute()
    {
        if($this->parent_id != null){
            return $this->parent->name . ' / ' . $this->name;
        } else {
            return $this->name;
        }
    }

}
