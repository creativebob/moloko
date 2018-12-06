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

class AlbumsCategory extends Model
{

    // Включаем кеш
    // use Cachable;

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
        'parent_id',
        'author_id',
        'system_item'
    ];

    // Компании
    public function company()
    {
    	return $this->belongsTo('App\Company');
    }

    // Альбомы
    public function albums()
    {
    	return $this->hasMany('App\Album');
    }


    // --------------------------------------- Запросы -----------------------------------------
    public function getIndex($request, $answer)
    {
        return $this->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer)
        ->template($answer)
        ->withCount('albums')
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->get();
    }

    public function getItem($id, $answer)
    {
        return $this->moderatorLimit($answer)->findOrFail($id);
    }

    // public function getIndexCount($answer, $request)
    // {
    //     return $this->moderatorLimit($answer)
    //     ->companiesLimit($answer)
    //     ->authors($answer)
    //     ->systemItem($answer)
    //     ->template($answer)
    //     ->count();
    // }
}
