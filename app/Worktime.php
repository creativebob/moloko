<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

// Заготовки
use App\Scopes\Traits\CompaniesLimitTraitScopes;
use App\Scopes\Traits\AuthorsTraitScopes;
use App\Scopes\Traits\SystemItemTraitScopes;
use App\Scopes\Traits\FilialsTraitScopes;
use App\Scopes\Traits\TemplateTraitScopes;
use App\Scopes\Traits\ModeratorLimitTraitScopes;

// Фильтры
use App\Scopes\Filters\BooklistFilter;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Worktime extends Model
{

    use Notifiable;
    use SoftDeletes;

    // Подключаем Scopes для главного запроса
    use CompaniesLimitTraitScopes;
    use AuthorsTraitScopes;
    use SystemItemTraitScopes;
    use FilialsTraitScopes;
    use TemplateTraitScopes;
    use ModeratorLimitTraitScopes;

    // Фильтры
    use BooklistFilter;


 	protected $dates = ['deleted_at'];

    protected $fillable = [

        'weekday', 
        'worktime_begin', 
        'worktime_interval', 
        'timeout_begin', 
        'timeout_interval', 
        'company_id', 
        'moderation', 

    ];

    // public function getWorktimeBeginAttribute($sec) {
    //     if($sec == Null){
    //         return $sec;
    //     } else 
    //         {
    //     		return secToString($sec);
    //         };
    // }

}

