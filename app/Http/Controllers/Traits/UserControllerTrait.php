<?php

namespace App\Http\Controllers\Traits;
use App\User;
use App\RoleUser;
use Illuminate\Support\Facades\DB;

trait UserControllerTrait
{

	public function createUser($request){

        // Подготовка: -------------------------------------------------------------------------------------

        // Получаем данные для авторизованного пользователя
        $user_auth = $request->user();

        // Скрываем бога
        $user_auth_id = hideGod($user_auth);
        $company_id = $user_auth->company_id;


        // СОЗДАЕМ ПОЛЬЗОВАТЕЛЯ ----------------------------------------------------------------------------

        $user = new User;
        $user_number = User::count();


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
        $user->access_block = $request->access_block;

        // Тип пользователя (Видимо, требует переработки. Идея устарела.)
        $user->user_type = $request->user_type;


        // Компания и филиал ----------------------------------------------------------
        $user->company_id = $request->user()->company->id;
        $user->filial_id = $request->filial_id;


        // Данные человека ------------------------------------------------------------
        
        $user->first_name =   $request->first_name;
        $user->second_name = $request->second_name;
        $user->patronymic = $request->patronymic;
        $user->sex = $user->sex ?? 1;
        $user->birthday = $request->birthday;

        // Литера (Особая идентификационная отметка, например в номере договора)
        $user->liter = $request->liter;


        // Контактные данные: ----------------------------------------------------------

        // Электронная почта
        if(!isset($request->email)){

            // Если не указана почта:
            // Генератор почты (Ну, вообще-то он не нужен...)
            $gen_string = str_random(8);
            $gen_email = $gen_string . '@mail.ru';
            $request->email = $gen_email;

        } else {

            // Если указана:
            $user->email = $request->email;            
        }

        // Мессенджеры
        $user->telegram_id = $request->telegram_id;
        
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

            // Если не указан логин, то генерируем
            $user->login = 'user_'.$user_number;
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
        $user->access_block = $request->access_block;

        // Тип пользователя (Видимо, требует переработки. Идея устарела.)
        $user->user_type = $request->user_type;


        // Компания и филиал ----------------------------------------------------------
        $user->company_id = $request->user()->company->id;
        $user->filial_id = $request->filial_id;


        // Данные человека ------------------------------------------------------------
        
        $user->first_name =   $request->first_name;
        $user->second_name = $request->second_name;
        $user->patronymic = $request->patronymic;
        $user->sex = $user->sex ?? 1;
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
            // $request->email = $gen_email;

            // Когда генератор выключен, пишем то, что пришло
            $user->email = $request->email;

        } else {

            // Если указана:
            $user->email = $request->email;            
        }

        // Мессенджеры
        $user->telegram_id = $request->telegram_id;
        
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
            savePhoto($request, $user);

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