<?php

namespace App\Http\Controllers;

use App\Company;
use App\Page;
use App\User;
use App\Department;
use App\RightRole;
use App\Action;
use App\Right;
use App\Entity;

// Модели которые отвечают за работу с правами + политики
use App\Role;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

// Запросы и их валидация
use Illuminate\Http\Request;
use App\Http\Requests\UpdateUser;

// Прочие необходимые классы
use Illuminate\Support\Facades\Log;

class GetAccessController extends Controller
{
    public function set(Request $request)
    {

        $mymass = [];

        $user = Auth::user();
        foreach ($user->staff as $staffer) {
            $mymass[] = $staffer->filial_id;
        }

        $auth_user_roles = $user->roles;

        // Создаем ассоциированный массив прав на авторизованного пользователя
        // В формате: Ключ"user-create-allow" и значение "1" если найдено правило.
        $user_access = [];
        foreach ($user->roles as $role) {
            foreach ($role->rights as $right){
                $user_access[$right->actionentity->alias_action_entity . "-" . $right->directive] = 1;
            }
        }

        $request->session()->put('access', $user_access);
        return redirect()->route('users.index');
    }
}
