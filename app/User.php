<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;

// Scopes для главного запроса
use App\Scopes\Traits\CompaniesLimitTraitScopes;
use App\Scopes\Traits\AuthorsTraitScopes;
use App\Scopes\Traits\SystemItemTraitScopes;
use App\Scopes\Traits\FilialsTraitScopes;
use App\Scopes\Traits\TemplateTraitScopes;
use App\Scopes\Traits\ModeratorLimitTraitScopes;
use App\Scopes\Filters\BooleanArrayFilter;

// Фильтры
use App\Scopes\Filters\Filter;
use App\Scopes\Filters\BooklistFilter;
// use App\Scopes\Filters\DateIntervalFilter;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Carbon\Carbon;
use App\Challenge;

class User extends Authenticatable
{

    use Notifiable;
    use SoftDeletes;

    // Включаем Scopes
    use CompaniesLimitTraitScopes;
    use AuthorsTraitScopes;
    use SystemItemTraitScopes;
    use FilialsTraitScopes;
    use TemplateTraitScopes;
    use ModeratorLimitTraitScopes;
    use BooleanArrayFilter;

    // Фильтры
    use Filter;
    use BooklistFilter;
    // use DateIntervalFilter;

    protected $dates = [
        'deleted_at',
        'birthday_date'
    ];

    protected $fillable = [
        'login',
        'login_date',

        'email',
        'password',
        'nickname',

        'external',

        'first_name',
        'second_name',
        'patronymic',
        'name',
        'gender',
        'birthday_date',

        'phone',
        'extra_phone',
        'telegram_id',
        'city_id',
        'address',

        'location_id',

        'orgform_status',
        // 'company_name',
        'user_inn',
        // 'kpp',
        // 'account_settlement',
        // 'account_correspondent',
        // 'bank',

        'passport_number',
        'passport_released',
        'passport_date',
        'passport_address',

        'specialty',
        'about',
        'degree',

        'user_type',
        'lead_id',
        'employee_id',
        'access_block',
        'company_id',
        'filial_id',
        'moderation',
        'photo_id',

        'display',
        'system',
        'moderation'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // // Фильтрация по городу
    // public function scopeUserFilter($query, $request)
    // {

    //     //Фильтруем по городу
    //     if($request->city_id){
    //         $query = $query->where('city_id', $request->city_id);
    //     }

    //     return $query;
    // }

    // // Фильтрация по статусу пользователя: клиент или сотрудник
    // public function scopeUserType($query, $user_type)
    // {
    //     if(isset($user_type)){
    //         if($user_type != "all"){
    //             return $query->where('user_type', '=', $user_type);
    //         }
    //     }
    // }

    // // Фильтрация по блокировке доступа:
    // public function scopeAccessBlock($query, $access_block)
    // {
    //     if(isset($access_block)){
    //         if($access_block != "all"){
    //             return $query->where('access_block', '=', $access_block);
    //         }
    //     }
    // }

    // КОНЕЦ БЛОКА ОПИСАНИЯ ФИЛЬТРОВ
    public function setBirthdayDateAttribute($value)
    {
        if (isset($value)) {
            $this->attributes['birthday_date'] = Carbon::createFromFormat('d.m.Y', $value);
        }
    }

    public function setPassportDateAttribute($value) {
        if($value == Null){
            return $value;
        } else
        {
            $date_parts = explode('.', $value);
            $this->attributes['passport_date'] = $date_parts[2].'-'.$date_parts[1].'-'.$date_parts[0];
        }
    }

//    public function setNameAttribute() {
//        $name = isset($this->attributes['first_name']) ? $this->attributes['first_name'] : '';
//        $name .= isset($this->attributes['second_name']) ? ' ' . $this->attributes['second_name'] : '';
//        $this->attributes['name'] = $name;
//    }

//    public function getBirthdayDateAttribute($value) {
//        if($value == Null){
//            return $value;
//        } else {
//            $date_parts = explode('-', $value);
//            $value = $date_parts[2].'.'.$date_parts[1].'.'.$date_parts[0];
//            return $value;
//        }
//    }

    public function getPassportDateAttribute($value) {
        if($value == Null){
            return $value;
        } else {
            $date_parts = explode('-', $value);
            $value = $date_parts[2].'.'.$date_parts[1].'.'.$date_parts[0];
            return $value;
        }
    }

    // Склеиваем имя и фамилию для юзера и выводим при обращении через name
//    public function getNameAttribute($value) {
//        $value = $this->first_name . ' ' . $this->second_name;
//        return $value;
//    }

    // Склеиваем имя и фамилию для юзера и выводим при обращении через name
    public function getNameReverseAttribute($value) {
        $value = $this->second_name . ' ' . $this->first_name;
        return $value;
    }

	public function getStafferFilialIdAttribute() {
		$access = session('access');
		$filial_id = $access['user_info']['filial_id'];
		return $filial_id;
	}

    // public function getPhoneAttribute($value) {

    //     if(strlen($value) == 11 ){
    //         if(mb_substr($value, 0, 4) == "8395"){
    //             $rest1 = mb_substr($value, 5/2, 2); // возвращает "abcd"
    //             $rest2 = mb_substr($value, 7/2, 2); // возвращает "abcd"
    //             $rest3 = mb_substr($value, 9/2, 2); // возвращает "abcd"
    //             $result = $rest1."-".$rest2."-".$rest3;
    //         } else {
    //             // $value = strtolower($value, "UTF-8");
    //             $rest1 = mb_substr($value, 0, 1); // возвращает "bcdef"
    //             $rest2 = mb_substr($value, 1, 3); // возвращает "bcd"
    //             $rest3 = mb_substr($value, 4, 3); // возвращает "abcd"
    //             $rest4 = mb_substr($value, 7, 2); // возвращает "abcdef"
    //             $rest5 = mb_substr($value, 9, 2); // возвращает "abcdef"
    //             $result = $rest1." (".$rest2.") ".$rest3."-".$rest4."-".$rest5;
    //         }
    //     }

    //     if(strlen($value) < 6) {
    //         $result = "Номер не указан";
    //     }

    //     return $result;
    // }

    // Получаем компанию.
    public function company()
    {
        return $this->belongsTo('App\Company');
    }

    public function filial()
    {
        return $this->belongsTo('App\Department');
    }

    // Получаем роли
    public function roles()
    {
        return $this->belongsToMany('App\Role')->withPivot('department_id');
    }

    // Оповещения
    public function notifications()
    {
        return $this->belongsToMany(Notification::class);
    }

    public function role_user()
    {
        return $this->hasMany('App\RoleUser');
    }

    // Получаем списки авторов
    public function booklists()
    {
        return $this->belongsToMany('App\Booklist');
    }

    // Получаем списки авторов
    public function booklists_author()
    {
        return $this->hasMany('App\Booklist', 'author_id');
    }

    // Получаем штат
    public function staff()
    {
        return $this->hasMany('App\Staffer');
    }

    public function news()
    {
        return $this->hasMany('App\News');
    }

    // Получаем должность
    public function staffer()
    {
        return $this->hasMany('App\Staffer')->first();
    }

    // Получаем сотрудников
    public function employees()
    {
        return $this->hasMany('App\Employee');
    }

    // Получаем роли
    public function role()
    {
        return $this->belongsToMany('App\RoleUser');
    }

    // Фото
    public function photo()
    {
        return $this->belongsTo(Photo::class)
//            ->withDefault([
//                'path' => '/img/system/plug/' . $this->sex == 1 ? 'avatar_small_man.png' : 'avatar_small_woman.png'
//            ])
            ;
    }

    // Получаем локацию пользователя
    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    // Получаем задачи
    public function challenges()
    {
        $result = $this->hasMany('App\Challenge', 'appointed_id');
        return $result;
    }

    // Получаем задачи
    public function challenges_work()
    {
        return $this->hasMany('App\Challenge', 'appointed_id')->whereNull('status');
    }

    // Получаем задачи
    public function challenges_active()
    {
        $result = $this->hasMany('App\Challenge', 'appointed_id')
        ->where('status', null)
        ->orderBy('deadline_date', 'desc');
        return $result;
    }

    // Получаем задачи на сегодня
    public function challenges_today()
    {
        return $this->hasMany('App\Challenge', 'appointed_id')->where('status', null)->whereDate('deadline_date', Carbon::today());
    }

    // Получаем задачи на завтра
    public function challenges_tomorrow()
    {
        return $this->hasMany('App\Challenge', 'appointed_id')->where('status', null)->whereDate('deadline_date', Carbon::tomorrow());
    }

    // Получаем задачи на завтра
    public function challenges_aftertomorrow()
    {
        return $this->hasMany('App\Challenge', 'appointed_id')->where('status', null)->whereDate('deadline_date', Carbon::today()->addDays(2));
    }

    // Получаем задачи На неделю
    public function challenges_week()
    {
        return $this->hasMany('App\Challenge', 'appointed_id')->where('status', null)->whereDate('deadline_date', '>=', Carbon::today())->whereDate('deadline_date', '<=', Carbon::today()->addDays(7));
    }

    // Получаем задачи будущие
    public function challenges_future()
    {
        return $this->hasMany('App\Challenge', 'appointed_id')->where('status', null)->whereDate('deadline_date', '>', Carbon::today()->addDays(30));
    }

    // Получаем задачи просроченные
    public function challenges_last()
    {
        return $this->hasMany('App\Challenge', 'appointed_id')->where('status', null)->whereDate('deadline_date', '<', Carbon::today());
    }

    // Получаем дидов без задач
    public function leads_without_challenges()
    {
        $result = $this->hasMany('App\Lead', 'manager_id')->where('challenges_active_count', 0)->whereNotIn('stage_id', [13, 14, 1, 12])->where('draft', null);
        return $result;
    }

    // Получаем отказы
    public function leads_cancel()
    {
        $result = $this->hasMany('App\Lead', 'manager_id')->whereIn('stage_id', [13])->where('draft', null);
        return $result;
    }

    // Получаем лиды которые на управлении
    public function leads_control()
    {
        $result = $this->hasMany('App\Lead', 'manager_id')->where('draft', null);
        return $result;
    }

    // Настройки
    public function settings()
    {
        $result = $this->hasMany('App\Setting');
        return $result;
    }


    // Телефоны

    // Основной
    public function main_phones()
    {
        return $this->morphToMany('App\Phone', 'phone_entity')->wherePivot('main', '=', 1)->whereNull('archive')->withPivot('archive');
    }

    public function getMainPhoneAttribute()
    {
        if(!empty($this->main_phones->first()))
        {
            $value = $this->main_phones->first();
        } else {
            $value = null;
        }
        return $value;
    }

    // Получаем правовую форму
    public function legal_form()
    {
        return $this->belongsTo('App\LegalForm', 'legal_form_id')->withDefault('Физлицо');
    }

    // Дополнительные
    public function extra_phones()
    {
        return $this->morphToMany('App\Phone', 'phone_entity')->whereNull('archive')->whereNull('main')->withPivot('archive');
    }

    // Все
    public function phones()
    {
        return $this->morphToMany('App\Phone', 'phone_entity');
    }



    // Лиды
    public function leads()
    {
        return $this->hasMany('App\Lead', 'manager_id');
    }

    // Лиды
    public function userLeads()
    {
        return $this->hasMany('App\Lead');
    }

    // Клиент
    public function client()
    {
        return $this->morphOne('App\Client', 'clientable');
    }

    // Бюджет
    public function badget()
    {
        return $this->hasMany('App\Lead', 'manager_id')->whereNotIn('stage_id', [13, 14, 1, 12]);
    }

    public function likesGoods()
    {
        return $this->belongsToMany('App\Models\Project\PricesGoods', 'like_prices_goods', 'user_id', 'prices_goods_id');
    }

    public function favoritesGoods()
    {
        return $this->belongsToMany('App\Models\Project\Goods', 'favorite_goods', 'user_id', 'goods_id');
    }




}
