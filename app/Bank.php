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
use App\Scopes\Traits\DealersTraitScopes;

// Подключаем кеш
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

// Фильтры
use App\Scopes\Filters\Filter;
use App\Scopes\Filters\BooklistFilter;
// use App\Scopes\Filters\DateIntervalFilter;

class Bank extends Model
{

    // Включаем кеш
    // use Cachable;

    use Notifiable;
    // use SoftDeletes;
    // 
    // Включаем Scopes
    use CompaniesLimitTraitScopes;
    use AuthorsTraitScopes;
    use SystemItemTraitScopes;
    use FilialsTraitScopes;
    use TemplateTraitScopes;
    use ModeratorLimitTraitScopes;
    use DealersTraitScopes;

    // Фильтры
    use Filter;
    use BooklistFilter;
    // use DateIntervalFilter;

    // protected $dates = ['deleted_at'];
    protected $fillable = [
        'company_id', 
        'bank_id', 
    ];

    // Получаем компании которые работают с банком
    public function contragents()
    {
        return $this->hasMany('App\Company', 'company_id');
    }

    // Получаем данные банка
    public function company()
    {
        return $this->belongsTo('App\Company', 'bank_id');
    }

    // Получаем все зарегистрированные счета
    public function bank_accounts()
    {
        return $this->hasMany('App\BankAccount', 'bank_id');
    }

    // Получаем автора
    public function author()
    {
        return $this->belongsTo('App\User', 'author_id');
    }

}
