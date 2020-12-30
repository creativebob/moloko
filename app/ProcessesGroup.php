<?php

namespace App;

use App\Models\System\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class ProcessesGroup extends BaseModel
{
    // Включаем кеш
    use Cachable,
        SoftDeletes;

    protected $fillable = [
        'name',
        'photo_id',
        'status',
        'description',
        'units_category_id',
        'rule_id',
        'album_id',

        'display',
        'system',
        'moderation'
    ];

    // Процессы
    public function processes()
    {
        return $this->hasMany(Process::class);
    }

    // Альбом
    public function album()
    {
        return $this->belongsTo(Album::class);
    }

    // Аватар
    public function photo()
    {
        return $this->belongsTo(Photo::class);
    }

    // Еденица измерения
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    // Еденица измерения
    public function units_category()
    {
        return $this->belongsTo(UnitsCategory::class);
    }

    // Категории
    // public function categories()
    // {
    //     return $this->morphedByMany(GoodsCategory::class, 'articles_group_entity');
    // }

    // Услуг
    public function services_categories()
    {
        return $this->morphedByMany(ServicesCategory::class, 'entity', 'processes_group_entity');
    }

    // Рабочих процессов
    public function workflows_categories()
    {
        return $this->morphedByMany(WorkflowsCategory::class, 'entity', 'processes_group_entity');
    }
}
