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

use App\Scopes\Traits\ManufacturersTraitScopes;

// Подключаем кеш
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
    

// Фильтры
use App\Scopes\Filters\Filter;
use App\Scopes\Filters\BooklistFilter;

class RawsProduct extends Model
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

    use ManufacturersTraitScopes;

    // Фильтры
    use Filter;
    use BooklistFilter;
    // use DateIntervalFilter;



    // Получаем категорию
    public function raws_category()
    {
        return $this->belongsTo('App\RawsCategory');
    }

     // Получаем категорию
    public function raws_articles()
    {
        return $this->hasMany('App\RawsArticle');
    }

    public function photo()
    {
        return $this->belongsTo('App\Photo');
    }

    // Получаем еденицу измерения
    public function unit()
    {
        return $this->belongsTo('App\Unit');
    }

    // Альбом
    public function album()
    {
        return $this->belongsTo('App\Album');
    }

    // Получаем автора
    public function author()
    {
        return $this->belongsTo('App\User', 'author_id');
    }
    
    // Получаем компанию.
    public function company()
    {
        return $this->belongsTo('App\Company');
    } 
}
