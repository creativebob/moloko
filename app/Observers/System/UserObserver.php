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

        $request = request();

        // Если не пришли имя, фамилия и отчество, парсим их из $request->name
        if(($request->first_name == null) && ($request->second_name == null) && ($request->patronymic == null) && (isset($request->name))){

            $mass_names = getNameUser($request->name);

            $user->first_name = $mass_names['first_name'];
            $user->second_name = $mass_names['second_name'];
            $user->patronymic = $mass_names['patronymic'];
            $user->gender = $mass_names['gender'];
            $user->nickname = $request->name;
        }

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
        $user->name = $user->first_name . ' ' . $user->second_name;

        if (empty($user->login)) {
            $usersCount = User::where('company_id', auth()->user()->company_id)
                ->withTrashed()
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
