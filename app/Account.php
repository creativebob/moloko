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

class Account extends Model
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
    use Filter;
    use BooklistFilter;
    // use DateIntervalFilter;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'name',
        'domain',
        'company_id',
        'appends',

        'external_id',
        'page_public_url',

        'display',
        'system',
        'moderation'
    ];

    // Склеиваем имя
    public function getFullNameAttribute() {
        $sourceName = $this->source_service->source->name . '-' . $this->source_service->name;
        return isset($this->name) ? $this->name . ' ' . $sourceName : $sourceName;
    }

    // Получаем компанию.
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    // Получаем автора
    public function author()
    {
        return $this->belongsTo(User::class);
    }

    // Получаем источник (сервис)
    public function source_service()
    {
        return $this->belongsTo(SourceService::class);
    }

}
