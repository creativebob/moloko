<?php

namespace App\Observers\System;

use App\Observers\System\Traits\Commonable;
use App\User;

class UserObserver
{
    use Commonable;

    public function creating(User $user)
    {
        $this->store($user);

        $user->password = bcrypt($user->password);
        $user->access_code = rand(1000, 9999);

        // Если не пришли имя, фамилия и отчество, парсим их из $request->name
//        if (($user->first_name == null) && ($user->second_name == null) && ($user->patronymic == null) && (isset($user->name))) {
//
//            $mass_names = getNameUser($user->name);
//
//            $user->first_name = $mass_names['first_name'];
//            $user->second_name = $mass_names['second_name'];
//            $user->patronymic = $mass_names['patronymic'];
//            $user->gender = $mass_names['gender'];
//            $user->nickname = $user->name;
//        }

        if (empty($user->filial_id)) {
            $authUser = auth()->user();
            if ($authUser) {
                $user->filial_id = $authUser->filial_id;
            } else {
                // TODO - 16.09.20 - Непоятно когда может возникнуть
                abort(403, 'При сохранении / обновлении юзера нет филиала из селекта или авторизованного пользователя');
            }
        }
    }

    public function updating(User $user)
    {
        $this->update($user);

        if ($user->isDirty('password')) {
            $user->password = bcrypt($user->password);
        }
    }

    public function deleting(User $user)
    {
        $this->destroy($user);
    }

    public function saving(User $user)
    {

        if ($user->first_name || $user->second_name) {
            $user->name = $user->first_name . ' ' . $user->second_name;
        }

        // TODO - 21.09.20 - ПОсле переработки getNameUser внедрить ее сюда.
        // Проверка на name при записи юзера со страницы лида
        if ($user->name) {
            $nameArray = explode(' ', $user->name);
            if (isset($nameArray[0])) {
                $user->first_name = $nameArray[0];
            }
            if (isset($nameArray[1])) {
                $user->second_name = $nameArray[1];
            }
        }

        if (empty($user->login)) {
            $usersCount = User::withTrashed()
                ->count();

            if ($usersCount == 0) {
                $number = 1;
            } else {
                $number = $usersCount + 1;
            }

            $user->login = "user_{$number}";
        }


    }
}
