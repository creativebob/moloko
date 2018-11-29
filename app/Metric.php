<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

// Scopes для главного запроса
use App\Scopes\Traits\CompaniesLimitTraitScopes;
use App\Scopes\Traits\AuthorsTraitScopes;
use App\Scopes\Traits\SystemItemTraitScopes;
use App\Scopes\Traits\FilialsTraitScopes;
use App\Scopes\Traits\TemplateTraitScopes;
use App\Scopes\Traits\ModeratorLimitTraitScopes;

// Подключаем кеш
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class Metric extends Model
{

    // Включаем кеш
    use Cachable;

    use SoftDeletes;

    // Включаем Scopes
    use CompaniesLimitTraitScopes;
    use AuthorsTraitScopes;
    use SystemItemTraitScopes;
    use FilialsTraitScopes;
    use TemplateTraitScopes;
    use ModeratorLimitTraitScopes;

    public function goods_categories()
    {
        return $this->morphedByMany('App\GoodsCategory', 'metric_entity');
    }

    public function raws_categories()
    {
        return $this->morphedByMany('App\RawsCategory', 'metric_entity');
    }

    // Получаем проодукцию
    // public function goods_categories()
    // {
    //     return $this->belongsToMany('App\GoodsCategory', 'metric_entity', 'metric_id', 'entity_id')->where('entity', 'goods_categories');
    // }

    // // Получаем проодукцию
    // public function raws_categories()
    // {
    //     return $this->belongsToMany('App\RawsCategory', 'metric_entity', 'metric_id', 'entity_id')->where('entity', 'raws_categories');
    // }

     // Получаем единицу измерения
    public function unit()
    {
        return $this->belongsTo('App\Unit');
    }

    // Получаем единицу измерения
    public function property()
    {
        return $this->belongsTo('App\Property');
    }

    public function values()
    {
        return $this->hasMany('App\MetricValue');
    }
}
