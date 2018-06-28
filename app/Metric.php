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

class Metric extends Model
{
     use SoftDeletes;

    // Включаем Scopes
    use CompaniesLimitTraitScopes;
    use AuthorsTraitScopes;
    use SystemItemTraitScopes;
    use FilialsTraitScopes;
    use TemplateTraitScopes;
    use ModeratorLimitTraitScopes;

    // Получаем проодукцию
    public function products_categories()
    {
        return $this->belongsToMany('App\ProductsCategory', 'metric_entity', 'metric_id', 'entity_id')->where('entity', 'products_categories');
    }

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
