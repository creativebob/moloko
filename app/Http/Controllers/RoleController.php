<?php

namespace App\Http\Controllers;

use App\Company;
use App\Page;
use App\User;

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

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $this->authorize('index', User::class);
        $user = Auth::user();
        $others_item['user_id'] = $user->id;
        $system_item = null;

        // Смотрим права на простотр системных.
         foreach ($user->roles as $role) {
            foreach ($role->rights as $right) {
                // Перебор всех прав пользователя
                if ($right->category_right_id == 3) {$others_item[$right->right_action] = $right->right_action;};
                if ($right->right_action == 'system-user') {$system_item = 1;};
                if ($right->right_action == 'get-users') {$others_item['all'] = 'all';};
            }
        }

        if (isset($user->company_id)) {
            // Если у пользователя есть компания
            $roles = Role::whereCompany_id($user->company_id)
                    ->otherItem($others_item)
                    ->systemItem($system_item) // Фильтр по системным записям
                    ->paginate(30);
        } else {
            // Если нет, то бог без компании
            if ($user->god == 1) {
              $roles = Role::paginate(30);
            };
        }
        // dd($users);

        $menu = Role::get();
        return view('roles.index', compact('roles', 'menu'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // $this->authorize('create', Role::class);

        $role = new Role;
        $menu = Page::get();
        return view('roles.create', compact('role', 'menu'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // $this->authorize('create', Role::class);

        $role = new Role;
        $role->role_name = $request->role_name;
        $role->role_description = $request->role_description;

        $role->save();
        return redirect('roles');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $role = Role::findOrFail($id);
        // $this->authorize('update', $role);

        $menu = Page::get();
        return view('roles.show', compact('role', 'menu'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $role = Role::findOrFail($id);
        // $this->authorize('update', $entity);

        $menu = Page::get();
        return view('roles.show', compact('role', 'menu'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);
        // $this->authorize('update', $role);
        $role->role_name = $request->role_name;
        $role->role_description = $request->role_description;

        $role->save();
        return redirect('roles');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
