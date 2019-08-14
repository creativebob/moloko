<?php

namespace App\Http\Controllers\Traits;

use App\User;
use App\RoleUser;
use App\Phone;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;

trait UserControllerTrait
{

	public function createUser($request){

        // Подготовка: -------------------------------------------------------------------------------------

        // Получаем данные для авторизованного пользователя
        $user_auth = $request->user();

        // Скрываем бога
        $user_auth_id = hideGod($user_auth);
        $company_id = $user_auth->company_id;

        // Проверка на существование:
        // Проверка по номеру телефона

        $finded_user = User::where('main');

        // СОЗДАЕМ ПОЛЬЗОВАТЕЛЯ ----------------------------------------------------------------------------

        $user = new User;
        $user_number = User::all()->last()->id;
        $user_number = $user_number + 1;

        // Данные для доступа ----------------------------------------------------------

        // Логин:
        if(!isset($request->login)){

            // Если не указан логин, то генерируем
            $user->login = 'user_'.$user_number;
        } else {

            // Если логин указан, то вписываем
            $user->login = $request->login;
        }

        // Пароль:
        $user->password = bcrypt($request->password);
        $user->nickname = $request->nickname;

        // Блокировка доступа
        if($request->access_block == 1){$user->access_block = 1;} else {$user->access_block = 0;};

        // Тип пользователя (Возможно, требует переработки. Идея устарела.)
        $user->user_type = $request->user_type ?? 0;


        // Компания и филиал ----------------------------------------------------------
        $user->company_id = $request->user()->company->id;
        $user->filial_id = $request->filial_id ?? $user_auth->filial_id;


        // Данные человека ------------------------------------------------------------

        $user->first_name = $request->first_name;
        $user->second_name = $request->second_name;
        $user->patronymic = $request->patronymic;
        $user->sex = $request->sex ?? 1;
        $user->birthday = $request->birthday;

        // Литера (Особая идентификационная отметка, например в номере договора)
        $user->liter = $request->liter;


        // Контактные данные: ----------------------------------------------------------

        // Электронная почта
        if($request->email == null){

            // Если не указана почта:
            // Генератор почты (Ну, вообще-то он не нужен...)
            // $gen_string = str_random(12);
            // $gen_email = $gen_string . '@mail.ru';
            // $user->email = $gen_email;

            $user->email = $request->email;

        } else {

            // Если указана:
            $user->email = $request->email;
        }

        // Мессенджеры
        $user->telegram = $request->telegram;

        // Добавляем локацию
        $user->location_id = create_location($request);


        // Паспортные и прочие регистрационные данные ---------------------------------
        $user->user_inn = $request->inn;
        $user->passport_address = $request->passport_address;
        $user->passport_number = $request->passport_number;
        $user->passport_released = $request->passport_released;
        $user->passport_date = $request->passport_date;


        // Профессиональные и личные особенности --------------------------------------
        $user->about = $request->about;
        $user->specialty = $request->specialty;
        $user->degree = $request->degree;
        $user->quote = $request->quote;

        $user->author_id = $request->user()->id;
        $user->save();

        if($user) {

            // Cохраняем или обновляем фото
            savePhoto($request, $user);

            // Сохряняем или обновляем телефон
            $phones = add_phones($request, $user);

            // Cохраняем или обновляем роли
            $result_setroles = setRoles($request, $user);
            return $user;

        } else {

            abort(403, 'Ошибка при создании пользователя!');
        }

        return $user;
    }

    public function createUserByPhone($phone, $request = null, $new_company = null){

        Log::info('Сработал трейт создания пользователя по номеру телефона');

        // Подготовка: -------------------------------------------------------------------------------------

        // Получаем данные для авторизованного пользователя
        $user_auth = Auth::user();

        $user_number = User::all()->last()->id;
        $user_number = $user_number + 1;

        $user = new User;
        $user->login = 'user_'.$user_number;
        $user->password = bcrypt(str_random(12));

        if($request != null){

            $user->first_name = $request->first_name;
            $user->second_name = $request->second_name;
            $user->patronymic = $request->patronymic;
        }

        $user->access_block = 0;
        $user->user_type = 0;

        // Компания и филиал ----------------------------------------------------------
        $user->company_id = $request->company_id ?? $user_auth->company->id ?? $new_company->id;
        $user->filial_id = $request->filial_id ?? $user_auth->filial_id ?? $new_company->filials->first()->id;

        $user->save();

        if($user) {

            // Если номера нет, пишем или ищем новый и создаем связь
            $new_phone = Phone::firstOrCreate(
                ['phone' => cleanPhone($phone)
            ], [
                'crop' => substr(cleanPhone($phone), -4),
            ]);

            $user->phones()->attach($new_phone->id, ['main' => 1]);
            return $user;

        } else {
            abort(403, 'Ошибка при создании пользователя по номеру телефона!');
        }
    }


	public function updateUser($request, $user){

        // Подготовка: -------------------------------------------------------------------------------------

        // Получаем данные для авторизованного пользователя
        $user_auth = $request->user();

        // Скрываем бога
        $user_auth_id = hideGod($user_auth);
        $company_id = $user_auth->company_id;


        // ОБНОВЛЯЕМ ПОЛЬЗОВАТЕЛЯ --------------------------------------------------------------------------


        // Данные для доступа ----------------------------------------------------------

        // Логин:
        if(!isset($request->login)){
            
            if($user->login == null){

                // Если не указан логин, то генерируем
                $user_number = User::all()->last()->id;
                $user->login = 'user_'.$user_number;
            }

        } else {

            // Если логин указан, то вписываем
            $user->login = $request->login;
        }

        // Пароль:
        // Если пришел не пустой пароль
        if (isset($request->password)) {
            $user->password = bcrypt($request->password);
        }

        $user->nickname = $request->nickname;

        // Блокировка доступа
        if($request->access_block == 1){$user->access_block = 1;} else {$user->access_block = 0;};

        // Тип пользователя (Видимо, требует переработки. Идея устарела.)
        $user->user_type = $request->user_type ?? 0;

        // Компания и филиал ----------------------------------------------------------
        $user->company_id = $request->user()->company->id;
        $user->filial_id = $request->filial_id ?? $user_auth->filial_id;


        // Данные человека ------------------------------------------------------------

        $user->first_name =   $request->first_name;
        $user->second_name = $request->second_name;
        $user->patronymic = $request->patronymic;
        $user->sex = $request->sex ?? 1;
        $user->birthday = $request->birthday;

        // Литера (Особая идентификационная отметка, например в номере договора)
        $user->liter = $request->liter;



        // Контактные данные: ----------------------------------------------------------

        // Электронная почта
        if(!isset($request->email)){

            // Если не указана почта:
            // Генератор почты (Ну, вообще-то он не нужен...)

            // $gen_string = str_random(8);
            // $gen_email = $gen_string . '@mail.ru';
            // $user->email = $gen_email;

            // Когда генератор выключен, пишем то, что пришло
            $user->email = $request->email;

        } else {

            // Если указана:
            $user->email = $request->email;
        }

        // Мессенджеры
        $user->telegram = $request->telegram;

        // Добавляем локацию
        $user->location_id = create_location($request);


        // Паспортные и прочие регистрационные данные ---------------------------------
        $user->user_inn = $request->inn;
        $user->passport_address = $request->passport_address;
        $user->passport_number = $request->passport_number;
        $user->passport_released = $request->passport_released;
        $user->passport_date = $request->passport_date;


        // Профессиональные и личные особенности --------------------------------------
        $user->about = $request->about;
        $user->specialty = $request->specialty;
        $user->degree = $request->degree;
        $user->quote = $request->quote;

        $user->author_id = $request->user()->id;
        $user->save();

        // Модерируем (Временно)
        // if($answer['automoderate']){$user->moderation = null;};

        if($user) {

            // Cохраняем или обновляем фото
            $photo_id = savePhoto($request, $user);
            $user->photo_id = $photo_id;
            $user->save();

            // Сохряняем или обновляем телефон
            $phones = add_phones($request, $user);

            // Cохраняем или обновляем роли
            $result_setroles = setRoles($request, $user);

        } else {

            abort(403, 'Ошибка при создании пользователя!');
        }

        return $user;
    }


}