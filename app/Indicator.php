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

class Indicator extends Model
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

    protected $dates = ['deleted_at'];

    protected $fillable = [

    ];

    // Компания
    public function company()
    {
        return $this->belongsTo('App\Company');
    }

    // Автор
    public function author()
    {
        return $this->belongsTo('App\User');
    }

    // Категория
    public function indicators_category()
    {
        return $this->belongsTo('App\IndicatorsCategory', 'indicators_category_id');
    }

    // Категория
    public function category()
    {
        return $this->morphTo();
    }

    // Сущность
    public function entity()
    {
        return $this->belongsTo('App\Entity');
    }

    // Единица измерения
    public function unit()
    {
        return $this->belongsTo('App\Unit');
    }

    // План
    public function plans()
    {
        return $this->belongsTo('App\Plan');
    }

    // // Удиница измерения
    // public function unit()
    // {
    //     return $this->belongsTo('App\Unit');
    // }

}
