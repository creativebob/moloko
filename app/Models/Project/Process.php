<?php

namespace App\Models\Project;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use App\Models\Project\Traits\Publicable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Process extends Model
{
    use Publicable;
    use Cachable;
    use SoftDeletes;

    protected $with = [
//        'photo',
        'unit',
        'unit_length',
    ];

    // Группа
    public function group()
    {
        return $this->belongsTo('App\ProcessesGroup', 'processes_group_id');
    }

    // Должности
    public function positions()
    {
        return $this->belongsToMany('App\Position', 'process_position')
//            ->withPivot([
//                'value',
//            ])
            ;
    }

    // Производитель
    public function manufacturer()
    {
        return $this->belongsTo('App\Manufacturer');
    }


    // Еденица измерения
    public function unit()
    {
        return $this->belongsTo('App\Unit');
    }

    // Еденица измерения продолжительности
    public function unit_length()
    {
        return $this->belongsTo('App\Unit');
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
