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
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphTo; 

// Фильтры
// use App\Scopes\Filters\Filter;
// use App\Scopes\Filters\BooklistFilter;
// use App\Scopes\Filters\DateIntervalFilter;

class Cost extends Model
{

    // Включаем кеш
//    use Cachable;

    use Notifiable;
    // use SoftDeletes;

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

    // public $timestamps = false;
    
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    protected $fillable = [
        'min',
        'max',
        'average',
        'serial',
        'author_id',
        'created_at',
        'company_id',
        'filial_id',
	    
        'cmv_id',
        'cmv_type',
        'manufacturer_id'
    ];

    public function cmv()
    {
        return $this->morphTo();
    }

    // История
    public function history()
    {
        return $this->hasMany(CostsHistory::class, 'cost_id');
    }
    
}