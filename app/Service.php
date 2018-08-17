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
use App\Scopes\Traits\SuppliersTraitScopes;

// use Illuminate\Support\Facades\Auth;

// Подключаем кеш
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
    

// Фильтры
use App\Scopes\Filters\Filter;
use App\Scopes\Filters\BooklistFilter;

class Service extends Model
{

	// Включаем кеш
    // use Cachable;

    use Notifiable;
    use SoftDeletes;

    // Включаем Scopes
    use CompaniesLimitTraitScopes;
    use AuthorsTraitScopes;
    use SystemItemTraitScopes;
    use FilialsTraitScopes;
    use TemplateTraitScopes;
    use ModeratorLimitTraitScopes;
    use SuppliersTraitScopes;

    // Фильтры
    use Filter;
    use BooklistFilter;
    // use DateIntervalFilter;

    public function services_article()
    {
        return $this->belongsTo('App\ServicesArticle');
    }

    // Получаем компанию.
    public function company()
    {
        return $this->belongsTo('App\Company');
    }

    // Получаем автора
    public function author()
    {
        return $this->belongsTo('App\User', 'author_id');
    }

    // Получаем альбом
    public function album()
    {
        return $this->belongsTo('App\Album');
    }

    public function photo()
    {
        return $this->belongsTo('App\Photo');
    }
}
