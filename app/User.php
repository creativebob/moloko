<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Scopes\ModerationScope;
use Illuminate\Foundation\Auth\User as Authenticatable;


class User extends Authenticatable
{

    use Notifiable;
    use SoftDeletes;


    //   /**
    //  * The "booting" method of the model.
    //  *
    //  * @return void
    //  */
    // protected static function boot()
    // {
    //     parent::boot();

    //     static::addGlobalScope(new ModerationScope);
    // }

  /**
   * Получить запись с именем группы доступа
   */
  // public function group_action()
  // {
  //   return $this->BelongsTo('App\Role', 'group_action_id');
  // }

  // public function group_locality()
  // {
  //   return $this->BelongsTo('App\Role', 'group_locality_id');
  // }

    // БЛОК ОПИСАНИЯ ФИЛЬТРОВ:


    // Фильтрация для показа системных записей
    public function scopeSystemItem($query, $system_item)
    {
        if(isset($system_item)){
          return $query->orWhere('system_item', 1);
        } else {return $query;};
    }

        // Фильтрация для показа авторов
    public function scopeAuthors($query, $authors, $filials)
    {


        if(isset($authors)){

            if($authors['authors_id'] == null){
                dd($authors);

                // Получаем записи авторов которых нам открыли - получаем записи созданные нами - получаем себя
                return $query->Where('author_id', $authors['user_id'])->orWhere('id', $authors['user_id']);


            } else {


                // $authors['authors_id'] = [4, 5, 6, 7];
                // 
                // // Получаем записи авторов которых нам открыли - получаем записи созданные нами - получаем себя
                return $query->WhereIn('author_id', $authors['authors_id'])->orWhere('author_id', $authors['user_id'])->orWhere('id', $authors['user_id']);

                // Получаем записи авторов которых нам открыли - получаем записи созданные нами - получаем себя
                // return $query->whereHas('staff', function ($query) use ($filials){
                //   $query->whereIn('filial_id', $filials);
                // })->WhereIn('author_id', $authors['authors_id'])->orWhere('author_id', $authors['user_id'])->orWhere('id', $authors['user_id']);
            }

          } else {
  
              // Без ограничений

               return $query;
          }
    }

         // Фильтрация по филиалу
    public function scopeFilials($query, $filials)
    {



            if($filials == null){

              return $query;

            } else {
           
                // dd($filials);
                // Получаем записи авторов которых нам открыли - получаем записи созданные нами - получаем себя
                return $query->whereIn('filial_id', $filials);
            }
    }

    // Фильтрация по статусу пользователя: клиент или сотрудник
    public function scopeUserType($query, $user_type)
    {
        if(isset($user_type)){
            if($user_type != "all"){return $query->where('user_type', '=', $user_type);}
        }
    }

    // Фильтрация по блокировке доступа: 
    public function scopeAccessBlock($query, $access_block)
    {
        if(isset($access_block)){
            if($access_block != "all"){return $query->where('access_block', '=', $access_block);}
        }
    }

    // КОНЕЦ БЛОКА ОПИСАНИЯ ФИЛЬТРОВ


    public function setBirthdayAttribute($value) {
        if($value == Null){
            return $value;
        } else 
            {
                $date_parts = explode('.', $value);
                $this->attributes['birthday'] = $date_parts[2].'-'.$date_parts[1].'-'.$date_parts[0];
            };
    }

    public function setPassportDateAttribute($value) {
        if($value == Null){
            return $value;
        } else 
            {
                $date_parts = explode('.', $value);
                $this->attributes['passport_date'] = $date_parts[2].'-'.$date_parts[1].'-'.$date_parts[0];
            };
    }

    public function getBirthdayAttribute($value) {
        if($value == Null){
            return $value;
        } else 
            {
                $date_parts = explode('-', $value);
                $value = $date_parts[2].'.'.$date_parts[1].'.'.$date_parts[0];
                return $value;
            };
    }

    public function getPassportDateAttribute($value) {
        if($value == Null){
            return $value;
        } else 
            {
                $date_parts = explode('-', $value);
                $value = $date_parts[2].'.'.$date_parts[1].'.'.$date_parts[0];
                return $value;
            };
    }

    // public function getContragentStatusAttribute($value) {
    //     if($value == 1){
    //         $value = "Сотрудник";
    //     } elseif($value == 2) {
    //         $value = "Клиент";
    //     };
    //     return $value;
    // }

    // public function setAccessBlockAttribute($value) {
    //     if($value == Null){
    //         $value = "Открыт";
    //     } elseif($value == 1) {
    //         $value = "Заблокирован";
    //     };
    //     return $value;
    // }


    public function getPhoneAttribute($value) {
        
        if(strlen($value) == 11 ){
            if(mb_substr($value, 0, 4) == "8395"){
                $rest1 = mb_substr($value, 5/2, 2); // возвращает "abcd"
                $rest2 = mb_substr($value, 7/2, 2); // возвращает "abcd"
                $rest3 = mb_substr($value, 9/2, 2); // возвращает "abcd"
                $result = $rest1."-".$rest2."-".$rest3;
            } else {
                // $value = strtolower($value, "UTF-8");
                $rest1 = mb_substr($value, 0, 1); // возвращает "bcdef"
                $rest2 = mb_substr($value, 1, 3); // возвращает "bcd"
                $rest3 = mb_substr($value, 4, 3); // возвращает "abcd"
                $rest4 = mb_substr($value, 7, 2); // возвращает "abcdef"
                $rest5 = mb_substr($value, 9, 2); // возвращает "abcdef"
                $result = $rest1."(".$rest2.") ".$rest3."-".$rest4."-".$rest5;
            };
        };

        if(strlen($value) < 6){
            $result = "Номер не указан";
        };

        return $result;
    }



    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'login', 
        'email', 
        'password', 
        'nickname', 

        'first_name', 
        'second_name', 
        'patronymic', 
        'sex', 
        'birthday', 

        'phone', 
        'extra_phone', 
        'telegram_id', 
        'city_id', 
        'address', 

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

        'user_type', 
        'lead_id', 
        'employee_id', 
        'access_block', 
        'company_id', 
        'filial_id', 

    ];



    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
  * Получаем компанию.
  */
  public function company()
  {
    return $this->belongsTo('App\Company');
  }

    /**
  * Получаем роли.
  */
  public function roles()
  {
    return $this->belongsToMany('App\Role')->withPivot('department_id');
  }


    /**
  * Получаем списки авторов
  */
  public function booklists()
  {
    return $this->belongsToMany('App\Booklist');
  }


    /**
  * Получаем штат.
  */
  public function staff()
  {
    return $this->hasMany('App\Staffer');
  }
    /**
  * Получаем сотрудников.
  */
  public function employees()
  {
    return $this->hasMany('App\Employee');
  }

}