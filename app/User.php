<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'login', 
        'email', 
        'password', 
        'nickname', 

        'first_name', 
        'second_name', 
        'patronymic', 
        'sex', 
        'birthday' , 

        'phone', 
        'extra_phone', 
        'telegram_id', 
        'city_id', 
        'address', 

        'orgform_status', 
        'company_name', 
        'inn', 
        'kpp', 
        'settlement_account', 
        'correspondent_account', 
        'bank', 

        'number_passport', 
        'released_passport', 
        'date_passport', 
        'address_passport', 

        'contragent_status', 
        'lead_id', 
        'employee_id', 
        'block_access', 
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
