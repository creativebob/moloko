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
use App\Scopes\Filters\DateIntervalFilter;

class Employee extends Model
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
    use DateIntervalFilter;

    protected $dates = [
        'deleted_at',
        'employment_date',
        'dismissal_date'
    ];
    protected $fillable = [
        'company_id',
        'staffer_id',
        'user_id',
        'employment_date',
        'dismissal_date',
        'dismissal_description',
        'author_id',
    ];

    // Получаем вакансию для сотрудников.
    public function staffer()
    {
        return $this->belongsTo('App\Staffer');
    }

    // Компания
    public function company()
    {
        return $this->belongsTo('App\Company');
    }

    // Получаем сотрудника.
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    // Получаем автора
    public function author()
    {
        return $this->belongsTo('App\User', 'author_id');
    }

    // --------------------------------------- Запросы -----------------------------------------
    // public function getIndex($request, $answer, $user)
    // {
    //     return $this->moderatorLimit($answer)
    //     ->companiesLimit($answer)

    //     // Так как сущность не филиала зависимая, но по факту
    //     // все таки зависимая через staff, то делаем нестандартную фильтрацию (прямо в запросе)
    //     ->when($answer['dependence'] == true, function ($query) use ($user) {
    //         return $query->whereHas('staffer', function($q) use ($user){
    //             $q->where('filial_id', $user->filial_id);
    //         });
    //     })

    //     // ->authors($answer)
    //     ->systemItem($answer) // Фильтр по системным записям
    //     ->booklistFilter($request)
    //     ->filter($request, 'position_id', 'staffer')
    //     ->filter($request, 'department_id', 'staffer')
    //     ->dateIntervalFilter($request, 'date_employment')
    //     ->orderBy('moderation', 'desc')
    //     ->orderBy('sort', 'asc')
    //     ->paginate(30);
    // }

    // public function getItem($id, $answer)
    // {
    //     return $this->moderatorLimit($answer)->findOrFail($id);
    // }

}