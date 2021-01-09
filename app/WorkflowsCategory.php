<?php

namespace App;

use App\Models\System\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkflowsCategory extends BaseModel
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

        'display',
        'system',
        'moderation'
    ];

    // Родитель
    public function parent()
    {
        return $this->belongsTo(WorkflowsCategory::class);
    }

    // Вложенные
    public function childs()
    {
        return $this->hasMany(WorkflowsCategory::class, 'parent_id');
    }

    // Рабочие процессы
    public function workflows()
    {
        return $this->hasMany(Workflow::class, 'category_id');
    }

    // Процессы
    public function processes()
    {
        return $this->belongsToMany(Process::class, 'workflows')
        ->where('draft', false)
        ->where('workflows.archive', false);
    }

    // Метрики
    public function metrics()
    {
        return $this->morphToMany(Metric::class, 'entity', 'preset_metric');
    }

    // Аватар
    public function photo()
    {
        return $this->belongsTo(Photo::class);
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
}
