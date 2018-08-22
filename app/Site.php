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
use App\Scopes\Filters\Filter;
use App\Scopes\Filters\BooklistFilter;
// use App\Scopes\Filters\DateIntervalFilter;

class Site extends Model
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
    use Filter;
    use BooklistFilter;
    // use DateIntervalFilter;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'name',
        'domain',
        'company_id',
        'appends',
    ];

    // Получаем страницы.
    public function pages()
    {
        return $this->hasMany('App\Page');
    }

    // Получаем пункты меню.
    public function menus()
    {
        return $this->belongsToMany('App\Menu');
    }

    // Получаем навигации.
    public function navigations()
    {
        return $this->hasMany('App\Navigation');
    }

    // Получаем компанию.
    public function company()
    {
        return $this->belongsTo('App\Company');
    }

    // Получаем новости.
    public function news()
    {
        return $this->hasMany('App\News');
    }

    // Получаем каталоги.
    public function catalogs()
    {
        return $this->hasMany('App\Catalog');
    }

    // Получаем автора
    public function author()
    {
        return $this->belongsTo('App\User', 'author_id');
    }

    // Получаем отделы
    public function departments()
    {
        return $this->belongsToMany('App\Department');
    }

}
