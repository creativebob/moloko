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

// Фильтры
use App\Scopes\Filters\Filter;
use App\Scopes\Filters\BooklistFilter;
// use App\Scopes\Filters\DateIntervalFilter;

class Place extends Model
{

    use Notifiable;
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

    ];

    // Фильтрация по городу
    public function scopeAuthorFilter($query, $request)
    {

        //Фильтруем по списку городов
        if($request->author_id){
            $query = $query->whereIn('author_id', $request->author_id);
        };

        return $query;
    }


    public function places_types()
    {
        return $this->belongsToMany('App\PlacesType', 'rooms', 'place_id', 'places_type_id');
    }


    public function author()
    {
        return $this->belongsTo('App\User', 'author_id');
    }

    // Получаем локацию компании
    public function location()
    {
        return $this->belongsTo('App\Location');
    }
    
}