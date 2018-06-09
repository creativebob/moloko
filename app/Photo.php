<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

// Scopes для главного запросаФ
use App\Scopes\Traits\CompaniesLimitTraitScopes;
use App\Scopes\Traits\AuthorsTraitScopes;
use App\Scopes\Traits\SystemItemTraitScopes;
use App\Scopes\Traits\FilialsTraitScopes;
use App\Scopes\Traits\TemplateTraitScopes;
use App\Scopes\Traits\ModeratorLimitTraitScopes;

// Фильтры
use App\Scopes\Filters\Filter;
use App\Scopes\Filters\BooklistFilter;
// use App\Scopes\Filters\DateIntervalFilter;

class Photo extends Model
{
    use Notifiable;

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

    protected $fillable = [

    ];

    // Получаем компанию
    public function company()
    {
        return $this->belongsTo('App\Company');
    }

    public function cur_news()
    {
        return $this->hasOne('App\News');
    }

     public function avatar()
    {
        return $this->hasOne('App\Album');
    }

    // Получаем альбомы
    public function album()
    {
        return $this->belongsTo('App\Album');
    }

    // Получаем альбомы
    public function albums()
    {
        return $this->belongsToMany('App\Album', 'album_entity', 'entity_id', 'album_id')->where('entity', 'photos');
    }

    // Получаем автора
    public function author()
    {
        return $this->belongsTo('App\User', 'author_id');
    }

    public function user()
    {
        return $this->hasOne('App\User');
    }
}
