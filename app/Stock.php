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

class Stock extends Model
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
        'description',
        'room_id',

        'filial_id',

        'is_production',
        'is_goods',

        'display',
        'system',
        'moderation'
    ];


    // Помещение
    public function room()
    {
        return $this->belongsTo(Site::class);
    }

    // Компания
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    // Автор
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    // Товары
    public function goods()
    {
        return $this->belongsToMany(Goods::class, 'stock_goods', 'stock_id', 'goods_id')
        ->withPivot([
            'count',
            'weight',
            'serial'
        ]);
    }

    // Сырье
    public function raws()
    {
        return $this->belongsToMany(Raw::class, 'stock_raw')
        ->withPivot([
            'count',
            'weight',
            'serial'
        ]);
    }

    public function filial()
    {
        return $this->belongsTo('App\Department', 'filial_id');
    }

}
