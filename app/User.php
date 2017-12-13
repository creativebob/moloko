<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;


class User extends Authenticatable
{

    use Notifiable;
    use SoftDeletes;

  /**
   * Получить запись с именем группы доступа
   */
  public function group_action()
  {
    return $this->BelongsTo('App\Access_group', 'group_action_id');
  }

  public function group_locality()
  {
    return $this->BelongsTo('App\Access_group', 'group_locality_id');
  }


    // БЛОК ОПИСАНИЯ ФИЛЬТРОВ:

    // Фильтрация по статусу пользователя: клиент или сотрудник
    public function scopeСontragent($query, $user_type)
    {
        if($user_type == "all"){
            return $query;
            } else {
            return $query->where('user_type', '=', $user_type); 
        }
    }

    // Фильтрация по блокировке доступа: 
    public function scopeAccessBlock($query, $access_block)
    {
        if($access_block == "all"){
            return $query;
            } else {
            return $query->where('access_block', '=', $access_block); 
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
        'group_action_id', 
        'group_locality_id', 

    ];



    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
}