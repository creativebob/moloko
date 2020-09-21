<?php

namespace App\Http\Controllers\System\Traits;

use App\User;

trait Userable
{
    /**
     * Сохранение только что созданного русурса
     *
     * @param null $entityAlias
     * @return User|\Illuminate\Database\Eloquent\Model
     */
    public function storeUser()
    {

        $request = request();
        $data = $request->input();

        $location = $this->getLocation();
        $data['location_id'] = $location->id;

        $user = User::create($data);

        if (!$user) {
            abort(403, __('errors.store'));
        }
//            $this->setPassword($user);

        $photoId = $this->getPhotoId($user);
        $user->photo_id = $photoId;
        $user->saveQuietly();

        $this->savePhones($user);

        // Cохраняем или обновляем роли
        $result_setroles = setRoles($request, $user);

        logs('users')->info("Создан пользователь. Id: [{$user->id}]");

        return $user;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param $user
     * @return mixed
     */
    public function updateUser($user)
    {
        $request = request();

        $data = $request->input();

        $location = $this->getLocation();
        $data['location_id'] = $location->id;

        $photoId = $this->getPhotoId($user);
        $data['photo_id'] = $photoId;

        $res = $user->update($data);

        if (!$res) {
            abort(403, __('errors.update'));
        }

//            $this->setPassword($user);

        $this->savePhones($user);

        // Cохраняем или обновляем роли
        $result_setroles = setRoles($request, $user);

        logs('users')->info("Обновлен пользователь. Id: [{$user->id}]");

        return $user;
    }

    public function checkUserByPhone($entityAlias = null, $siteId = null)
    {
        $user = User::where('company_id', auth()->user()->company_id)
            ->whereHas('main_phones', function ($q) {
                $q->where('phone', cleanPhone(request()->main_phone));
            })
            ->when($entityAlias == 'users', function ($q) use ($siteId) {
                $q->where('site_id', $siteId);
            })
            ->when($entityAlias == 'clients', function ($q) {
                $q->where(function ($q) {
                    $q->where('site_id', '!=', 1)
                        ->orWhereNull('site_id');
                });
            })
            ->when($entityAlias == 'employees', function ($q) {
                $q->where('site_id', 1);
            })
            ->first();
//        dd($user);

        return $user;
    }

    /**
     * Устанавливаем пароль для пользователя
     *
     * @param $user
     */
    public function setPassword($user)
    {
        $request = request();

        if (isset($request->password)) {
            $user->password = bcrypt($request->password);
            $user->saveQuietly();
        }
    }
}
