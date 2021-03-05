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
// use App\Scopes\Filters\DateIntervalFilter;

class Manufacturer extends Model
{

    // Включаем кеш
    use Cachable;

    use Notifiable;
    use SoftDeletes;
    //
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

    // protected $dates = ['deleted_at'];
    protected $fillable = [
        'manufacturer_id',

        'description',
        'is_partner',

        'display',
        'system',
        'moderation'
    ];

    // Получаем компанию.
    public function company()
    {
        return $this->belongsTo('App\Company', 'manufacturer_id');
    }

     public function clients()
    {
        return $this->hasMany('App\Company', 'company_id');
    }

    // Получаем автора
    public function author()
    {
        return $this->belongsTo('App\User', 'author_id');
    }

    // Вложения производителя
    public function attachments()
    {
        return $this->hasManyThrough('App\Attachment', 'App\Article', 'manufacturer_id');
    }

    // Товары производителя
    public function goods()
    {
        return $this->hasManyThrough('App\Goods', 'App\Article', 'manufacturer_id');
    }

    // Упаковка производителя
    public function containers()
    {
        return $this->hasManyThrough('App\Container', 'App\Article', 'manufacturer_id');
    }

    // Сырье производителя
    public function raws()
    {
        return $this->hasManyThrough('App\Raw', 'App\Article', 'manufacturer_id');
    }

    public function impacts()
    {
        return $this->hasManyThrough(Impact::class, 'App\Article', 'manufacturer_id');
    }

    public function tools()
    {
        return $this->hasManyThrough(Tool::class, 'App\Article', 'manufacturer_id');
    }
}
