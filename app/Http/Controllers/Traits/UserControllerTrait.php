<?php

namespace App\Http\Controllers\Traits;
use App\User;

trait UserControllerTrait
{

	public function createUser($request){

        $user = new User;

        $user_number = User::count();

        if(!isset($request->login)){
            $user->login = 'user_'.$user_number;
        } else {
            $user->login = $request->login;
        }

        if(!isset($request->email)){
            $gen_string = str_random(8);
            $gen_email = $gen_string . '@mail.ru';
            $request->email = $gen_email;
        } else {
            $user->email = $request->email;            
        }


        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->nickname = $request->nickname;
        $user->liter = $request->liter;

        $user->first_name =   $request->first_name;
        $user->second_name = $request->second_name;
        $user->patronymic = $request->patronymic;
        $user->sex = $user->sex ?? 1;
        $user->birthday = $request->birthday;
        $user->company_id = $request->user()->company->id;
        $user->telegram_id = $request->telegram_id;
        
        // Добавляем локацию
        $user->location_id = create_location($request);

        $user->user_inn = $request->inn;

        $user->passport_address = $request->passport_address;
        $user->passport_number = $request->passport_number;
        $user->passport_released = $request->passport_released;
        $user->passport_date = $request->passport_date;

        $user->about = $request->about;
        $user->specialty = $request->specialty;
        $user->degree = $request->degree;
        $user->quote = $request->quote;

        $user->user_type = $request->user_type;
        $user->lead_id = $request->lead_id;
        $user->employee_id = $request->employee_id;
        $user->access_block = $request->access_block;

        $user->author_id = $request->user()->id;
        $user->save();

        // Если запись удачна - будем записывать связи
        if($user){

            add_phones($request, $user);

        } else {

            abort(403, 'Ошибка записи пользователя');
        };

        return $user;
    }


	public function updateUser($request, $user){

        $filial_id = $request->filial_id;

        // Обновляем локацию
        $user = update_location($request, $user);

        $user->login = $request->login;
        $user->email = $request->email;

        // Если пришел не пустой пароль
        if (isset($request->password)) {
            $user->password = bcrypt($request->password);
        }

        $user->nickname = $request->nickname;
        $user->liter = $request->liter;

        $user->first_name = $request->first_name;
        $user->second_name = $request->second_name;
        $user->patronymic = $request->patronymic;
        $user->sex = $request->sex;
        $user->birthday = $request->birthday;

        // Телефон
        $phones = add_phones($request, $user);

        $user->telegram_id = $request->telegram_id;

        $user->orgform_status = $request->orgform_status;

        $user->user_inn = $request->inn;

        $user->passport_address = $request->passport_address;
        $user->passport_number = $request->passport_number;
        $user->passport_released = $request->passport_released;
        $user->passport_date = $request->passport_date;

        $user->about = $request->about;
        $user->specialty = $request->specialty;
        $user->degree = $request->degree;
        $user->quote = $request->quote;

        $user->user_type = $request->user_type;
        $user->lead_id = $request->lead_id;
        $user->employee_id = $request->employee_id;
        $user->access_block = $request->access_block;

        $user->filial_id = $request->filial_id;

        // Модерируем (Временно)
        // if($answer['automoderate']){$user->moderation = null;};

        $user->save();

        if ($user) {

            // Cохраняем / обновляем фото
            savePhoto($request, $user);

        } else {
            abort(403, 'Ошибка при обновлении пользователя!');
        }



    }


}