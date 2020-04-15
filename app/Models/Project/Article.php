<?php

namespace App\Models\Project;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use App\Models\Project\Traits\Publicable;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use Publicable;
    use Cachable;

    protected $with = [
        'photo',
        'unit',
        'unit_weight',

        // TODO - 15.04.20 - Костыль для вкусняшки (пока еще не разведены запросы для пагинации и общей кучи)
        'attachments.article.photo',
    ];

    // Группа
    public function group()
    {
        return $this->belongsTo('App\ArticlesGroup', 'articles_group_id');
    }

    // Состав
    public function raws()
    {
        return $this->belongsToMany('App\Raw', 'article_raw')
            ->where([
                'display' => true,
            ])
            ->withPivot([
                'value',
                'useful',
                'waste',
                'leftover',
                'leftover_operation_id'
            ]);
    }

    // Упаковка
    public function containers()
    {
        return $this->belongsToMany('App\Container', 'article_container')
            ->where([
                'display' => true,
            ])
            ->withPivot([
                'value',
                'useful',
                'waste',
                'leftover',
                'leftover_operation_id'
            ]);
    }

    // Вложения
    public function attachments()
    {
        return $this->belongsToMany('App\Attachment', 'article_attachment')
            ->where([
                'display' => true,
            ])
            ->withPivot([
                'value',
                'useful',
                'waste',
                'leftover',
                'leftover_operation_id'
            ]);
    }

    // Производитель
    public function manufacturer()
    {
        return $this->belongsTo('App\Manufacturer');
    }

    // Аватар
    public function photo()
    {
        return $this->belongsTo('App\Photo');
    }

    // Единица измерения
    public function unit()
    {
        return $this->belongsTo('App\Unit');
    }

    // Еденица измерения веса
    public function unit_weight()
    {
        return $this->belongsTo('App\Unit');
    }

    // Еденица измерения обьема
    public function unit_volume()
    {
        return $this->belongsTo('App\Unit');
    }

    // Вес
    public function getWeightTransAttribute()
    {

        if (isset($this->unit_id)) {
            if(isset($this->unit_weight)){
                $weight = $this->weight / $this->unit_weight->ratio;
            } else {
                $weight = $this->weight / $this->unit->ratio;
            }

        } else {
            $weight = $this->weight / $this->group->unit->ratio;
        }

        return $weight;
    }

    public function getWeightGramAttribute()
    {
        return $this->weight * 1000;
    }

    // Объем
    public function getVolumeTransAttribute()
    {

        if (isset($this->unit_id)) {
            if(isset($this->unit_volume)){
                $volume = $this->volume / $this->unit_volume->ratio;
            } else {
                $volume = $this->volume / $this->unit->ratio;
            }

        } else {
            $volume = $this->volume / $this->group->unit->ratio;
        }

        return $volume;
    }
}
