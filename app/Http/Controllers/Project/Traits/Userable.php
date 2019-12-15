<?php

namespace App\Http\Controllers\Project\Traits;

use Illuminate\Support\Facades\Cookie;

use App\User;
use App\Phone;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

trait Userable
{

    // Функция создания пользователя для сайта исключительно по номеру телефона.

    public function createUserByPhoneFromSite($phone_from_site, $site){

        Log::info('Сработал трейт создания пользователя сайта по номеру телефона');

        $phone_cleaned = cleanPhone($phone_from_site);
        $phone = Phone::firstOrCreate(['phone' => $phone_cleaned], ['crop' => substr($phone_cleaned, -4)]);

        $user_number = User::order('id', 'desc')->all()->last()->id;
        $user_number = $user_number + 1;

        $user = new User;
        $user->login = 'user_'.$user_number;
        $user->password = bcrypt(str_random(12));

        // Генерируем код доступа
        $user->access_code = rand(1000, 9999);

        $user->access_block = 0;
        $user->site_id = $site->id;
        $user->company_id = $site->company->id;
        $user->filial_id = $site->filial->id;

        $user->save();

        Log::info('Пользователь сохранен');

        if($user) {
            $user->phones()->attach($phone->id, ['main' => 1]);
            Log::info('Выполнена привязка номера телефона к пользователю');

            return $user;
        } else {
            
            abort(403, 'Ошибка при создании пользователя по номеру телефона!');
        }
    }

}
