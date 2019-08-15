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

// Фильтры
// use App\Scopes\Filters\Filter;
// use App\Scopes\Filters\BooklistFilter;
// use App\Scopes\Filters\DateIntervalFilter;

class Navigation extends Model
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

    // Фильтры
    // use Filter;
    // use BooklistFilter;
    // use DateIntervalFilter;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'name',
        'alias',
        'site_id',
        'company_id',
        'navigations_category_id',

        'display',
        'system',
        'moderation'
    ];

    // Пункты навигации
    public function menus()
    {
        return $this->hasMany('App\Menu');
    }

    // Сайт
    public function site()
    {
        return $this->belongsTo('App\Site');
    }

    // Категории навигации
    public function category()
    {
        return $this->belongsTo('App\NavigationsCategory', 'navigations_category_id');
    }

    // Автор
    public function author()
    {
        return $this->belongsTo('App\User', 'author_id');
    }

    // Расположение
    public function align()
    {
        return $this->belongsTo('App\Align');
    }

    // Предок
    public function ancestor()
    {
        return $this->belongsTo('App\Site', 'site_id');
    }
}
