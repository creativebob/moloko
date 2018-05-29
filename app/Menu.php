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
// use App\Scopes\Filters\Filter;
// use App\Scopes\Filters\BooklistFilter;
// use App\Scopes\Filters\DateIntervalFilter;

class Menu extends Model
{

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
    
    // protected $table = 'menu';
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'name',
        'parent_id',
        'page_id',
    ];

    // Получаем навигацию меню.
    public function navigation()
    {
        return $this->belongsTo('App\Navigation');
    }

    // Получаем страницу меню.
    public function page()
    {
        return $this->belongsTo('App\Page');
    }

    // Получаем сайты.
    public function sites()
    {
        return $this->belongsToMany('App\Site');
    }
}
