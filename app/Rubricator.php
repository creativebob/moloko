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

class Rubricator extends Model
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
        'slug',
        'description',

        'display',
        'system',
        'moderation'
    ];

    // Пункты
    public function items()
    {
        return $this->hasMany(RubricatorsItem::class);
    }


    // Сайты
    public function sites()
    {
        return $this->belongsToMany(Site::class, 'rubricator_site');
    }

    // Аавтор
    public function author()
    {
        return $this->belongsTo(User::class);
    }

    // Аватар
    public function photo()
    {
        return $this->belongsTo(Photo::class);
    }
}
