<?php

namespace App\Observers;

use App\User;

// use App\Observers\Traits\CommonTrait;

class UserObserver
{
    // use CommonTrait;

    public function creating(User $user)
    {
        $request = request();

        // Если не пришли имя, фамилия и отчество, парсим их из $request->name
        if(($request->first_name == null) && ($request->second_name == null) && ($request->patronymic == null) && (isset($request->name))){
            
            $mass_names = getNameUser($request->name);

            $user->first_name = $mass_names['first_name'];
            $user->second_name = $mass_names['second_name'];
            $user->patronymic = $mass_names['patronymic'];
            $user->sex = $mass_names['gender'];
            $user->nickname = $request->name;

        }
    }

    public function updating(User $user)
    {
        // $this->update($user);
    }

    public function deleting(User $user)
    {
        // $this->destroy($user);
    }
}
