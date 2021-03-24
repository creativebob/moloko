<?php

namespace App\Http\Controllers\System\Traits;

use App\Department;
use App\Employee;
use App\Location;
use App\Page;
use App\Phone;
use App\Position;
use App\Role;
use App\Staffer;
use App\User;

trait Directorable
{
    // TODO - 15.09.20 - Создание директора вынесено в трейт, в дальнейшем думаю реализовывать через шаблон проетирования "Интерфейс"

    protected $company;
    protected $roleAlias;

    protected $filial;
    protected $position;
    protected $staffer;
    protected $role;
    protected $user;

    /**
     * Создание директора для компании (филиал, должность, роль, ставка, сотрудник)
     *
     * @param $company
     */
    public function getDirector($company, $roleAlias = 'base')
    {
        $this->company = $company;
        $this->roleAlias = $roleAlias;

        $company->load('director.user');

        if (optional($company->director)->user_id) {
            logs('companies')->info("У компании существует директор, обновляем данные, id: [{$company->director->user_id}]");

            $this->user = $this->updateUserDirector($company->director->user);

        } else {
            logs('companies')->info("===== НАЧАЛО СОЗДАНИЯ ДИРЕКТОРА =====");

            $this->filial = $this->storeFilial($this->company);
            $this->user = $this->storeUserDirector($this->filial);
            $this->position = $this->storePosition($this->company);
            $this->staffer = $this->storeStaffer($this->user, $this->position, $this->filial);
            $this->employee = $this->storeEmployee($this->user, $this->staffer);
            $this->role = $this->setRole($this->position, $this->filial, $this->user);

            logs('companies')->info("===== КОНЕЦ СОЗДАНИЯ ДИРЕКТОРА =====");
        }
    }

    /**
     * Создаем филиал для компании
     *
     * @param $company
     * @return Department|\Illuminate\Database\Eloquent\Model
     */
    public function storeFilial($company)
    {
        $filial = Department::make([
            'name' => "Филиал в городе {$company->location->city->name}",
            'location_id' => $company->location_id,
            'email' => $company->email,
        ]);

        $filial->author_id = 1;
        $filial->company_id = $company->id;
        $filial->saveQuietly();
//        dd($filial);

        logs('companies')->info("Создан филиал для компании [{$company->id}] с id: [{$filial->id}]");
        $this->savePhones($filial);

        return $filial;
    }

    /**
     * Поиск пользователя (сотрудника) по номеру телефона директора
     *
     * @param $item
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function getUserByPhone($item)
    {
        $user = User::where([
            'company_id' => $item->company_id,
            'site_id' => 1
        ])
            ->whereHas('main_phones', function ($q) {
                $q->where('phone', cleanPhone(request()->user_main_phone));
            })
            ->first();

        return $user;
    }

    /**
     * Создаем пользователя (Директора) для компании
     *
     * @param $filial
     * @return User|\Illuminate\Database\Eloquent\Model
     */
    public function storeUserDirector($filial)
    {

        $user = $this->getUserByPhone($filial);

        if (!$user) {
            $request = request();

            $usersCount = User::withTrashed()
                ->where([
                    'company_id' => $filial->company_id,
                    'site_id' => 1
                ])
                ->count();

            $location = $this->getLocation(null, $request->user_city_id, $request->user_address, null);

            $user = User::make([
                'second_name' => $request->user_second_name,
                'first_name' => $request->user_first_name,
                'name' => $request->user_first_name . ' ' . $request->user_second_name,
                'patronymic' => $request->user_patronymic,
                'login' => $request->user_login ?? ($usersCount + 1),
                'access_code' => rand(1000, 9999),
                'filial_id' => $filial->id,
                'site_id' => 1,
                'location_id' => $location->id,
                'user_type' => 1
            ]);

            $user->author_id = 1;
            if ($request->has('user_password')) {
                $user->password = bcrypt($request->user_password);
            }
            $user->company_id = $filial->company_id;
            $user->saveQuietly();

            logs('companies')->info("Создан пользователь (Директор) для компании [{$filial->company_id}] с id: [{$user->id}]");

            if (isset($request->user_main_phone)) {
                $phone = Phone::firstOrCreate([
                    'phone' => cleanPhone($request->user_main_phone)
                ], [
                    'crop' => substr(cleanPhone($request->user_main_phone), -4),
                ]);

                $user->phones()->attach($phone->id, ['main' => 1]);
            }
        }

        return $user;
    }

    /**
     * Обновляем пользователя (Директора) для компании
     *
     * @param $user
     * @return mixed
     */
    public function updateUserDirector($user)
    {
        $checkUser = $this->getUserByPhone($user);

        if ($checkUser) {
            if ($checkUser->main_phone->phone != cleanPhone(request()->user_main_phone)) {
                return back()
                    ->withErrors(['msg' => 'Пользователь уже существует']);
            }
        }

        $request = request();

        $location = $this->getLocation(null, $request->user_city_id, $request->user_address, null);

        $user->first_name = $request->user_first_name;
        $user->second_name = $request->user_second_name;
        $user->patronymic = $request->user_patronymic;
        $user->name = $request->user_first_name . ' ' . $request->user_second_name;
        $user->location_id = $location->id;

        $user->login = $request->user_login;
        if ($request->has('user_password')) {
            $user->password = bcrypt($request->user_password);
        }
        $user->updateQuietly();

        logs('companies')->info("Обновлен пользователь (Директор) для компании [{$user->company_id}] с id: [{$user->id}]");

        if (isset($request->user_main_phone)) {
            $user->main_phones()->update([
                'main' => null
            ]);

            $phone = Phone::firstOrCreate([
                'phone' => cleanPhone($request->user_main_phone)
            ], [
                'crop' => substr(cleanPhone($request->user_main_phone), -4),
            ]);

            $user->phones()->attach($phone->id, ['main' => 1]);
        }

        return $user;
    }

    /**
     * Создаем должность директора для компании
     *
     * @param $company
     * @return Position|\Illuminate\Database\Eloquent\Model
     */
    public function storePosition($company)
    {
        $pageId = Page::where([
            'alias' => 'dashboard',
            'site_id' => 1
        ])
            ->value('id');

        $position = Position::make([
            'name' => 'Директор',
            'page_id' => $pageId,
            'direction' => true,
        ]);

        $position->author_id = 1;
        $position->company_id = $company->id;
        $position->saveQuietly();

        logs('companies')->info("Создана должность (Директор) для компании [{$company->id}]) с id: [{$position->id}]");

        return $position;
    }

    /**
     * Создаем ставку директора для компании
     *
     * @param $user
     * @param $position
     * @param $filial
     * @return Staffer|\Illuminate\Database\Eloquent\Model
     */
    public function storeStaffer($user, $position, $filial)
    {
        $staffer = Staffer::make([
            'user_id' => $user->id,
            'position_id' => $position->id,
            'department_id' => $filial->id,
            'filial_id' => $filial->id,
        ]);

        $staffer->author_id = 1;
        $staffer->company_id = $user->company_id;
        $staffer->saveQuietly();

        logs('companies')->info("Создана ставка (Директор) для компании [{$user->company_id}]) с id: [{$staffer->id}]");

        return $staffer;
    }

    /**
     * Создаем сотрудника директора для компании
     *
     * @param $user
     * @param $staffer
     * @return mixed
     */
    public function storeEmployee($user, $staffer)
    {
        $employee = Employee::make([
            'user_id' => $user->id,
            'staffer_id' => $staffer->id,
            'employment_date' => today()->format('d.m.Y'),
            'author_id' => 1
        ]);

        $employee->author_id = 1;
        $employee->company_id = $user->company_id;
        $employee->saveQuietly();
        logs('companies')->info("Создан сотрудник (Директор) для компании [{$user->company_id}]) с id: [{$employee->id}]");

        return $employee;
    }

    /**
     * Связываем созданную роль с должностью и пользователем
     *
     * @param $position
     * @param $filial
     * @param $user
     * @return mixed
     */
    public function setRole($position, $filial, $user)
    {
        $role = Role::where('alias', $this->roleAlias)
            ->first();

        if (empty($role)) {
            $role = Role::where('alias', 'base')
                ->first();
        }

        $position->roles()->attach($role->id);

        logs('companies')->info("Должность [{$position->id}] связана с ролью [{$role->id}]");

        $user->roles()->attach([$role->id => [
            'department_id' => $filial->id,
            'position_id' => $position->id,
        ]
        ]);

        logs('companies')->info("Пользователь [{$user->id}] связан с ролью [{$role->id}]");

        return $role;
    }
}
