<?php

namespace App;

use App\Models\System\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class Process extends BaseModel
{
    use Cachable,
        SoftDeletes;

    protected $fillable = [
        'name',
        'description',

        'processes_group_id',
        'processes_type_id',

        'internal',
        'manually',
        'external',

        'manufacturer_id',

        'kit',

        'cost_default',
        'cost_mode',
        'price_default',
        'price_mode',
        'price_rule_id',

        'video_url',

        'content',
        'seo_description',
        'keywords',

        'length',
        'unit_length_id',

        'photo_id',

        'draft',
        'unit_id',

        'display',
        'system',
        'moderation'
    ];

    // Группа
    public function group()
    {
        return $this->belongsTo(ProcessesGroup::class, 'processes_group_id');
    }

    // Товар
    // public function goods()
    // {
    //     return $this->hasMany(Goods');
    // }

    // Рабочие процессы
    public function workflows()
    {
        return $this->belongsToMany(Workflow::class, 'process_workflow')
        ->withPivot([
            'value',
         ]);
    }

    // Услуги
    public function services()
    {
        return $this->belongsToMany(Service::class, 'process_service')
            ->withPivot([
                'value',
            ]);
    }

    // Должности
    public function positions()
    {
        return $this->belongsToMany(Position::class, 'process_position')
//            ->withPivot([
//                'value',
//            ])
            ;
    }

    // Состав (набор)
    // public function set_compositions()
    // {
    //     return $this->morphedByMany(Article', 'articles_values')->withPivot('value');
    // }

    // Производитель
    public function manufacturer()
    {
        return $this->belongsTo(Company::class);
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

    // Услуга
    public function service()
    {
        return $this->hasOne(Service::class);
    }

    // Еденица измерения
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    // Еденица измерения продолжительности
    public function unit_length()
    {
        return $this->belongsTo(Unit::class);
    }

    public function in_services()
    {
        return $this->hasMany(Service::class, 'process_id');
    }

    public function in_workflows()
    {
        return $this->hasMany(Workflow::class, 'process_id');
    }


    // Продолжительность
    public function getLengthTransAttribute($value)
    {
        if (isset($this->unit_id)) {
            if(isset($this->unit_length_id)){
                $length = $this->length / $this->unit_length->ratio;
            } else {
                $length = $this->length / $this->unit->ratio;
            }
        } else {
            $length = $this->length / $this->group->unit->ratio;
        }

        return $length;
    }
}
