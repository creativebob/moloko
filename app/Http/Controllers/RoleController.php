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
        // $others_item['user_id'] = $user->id;
        // $system_item = null;

        // // Смотрим права на простотр системных.
        //  foreach ($user->roles as $role) {
        //     foreach ($role->rights as $right) {
        //         // Перебор всех прав пользователя
        //         if ($right->category_right_id == 3) {$others_item[$right->right_action] = $right->right_action;};
        //         if ($right->right_action == 'system-user') {$system_item = 1;};
        //         if ($right->right_action == 'get-users') {$others_item['all'] = 'all';};
        //     }
        // }

        if (isset($user->company_id)) {
            // Если у пользователя есть компания
            $roles = Role::whereCompany_id($user->company_id)
                    // ->otherItem($others_item)
                    // ->systemItem($system_item) // Фильтр по системным записям
                    ->paginate(30);
        } else {
            // Если нет, то бог без компании
            if ($user->god == 1) {
              $roles = Role::paginate(30);
            };
        }
        // dd($users);

        $menu = Page::get();
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

        $user = Auth::user();
        $departments_list = Department::where('company_id', $user->company_id)->whereFilial_status(1)->pluck('department_name', 'id');

        $role = new Role;
        $menu = Page::get();
        return view('roles.create', compact('role', 'menu', 'departments_list'));
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

        $user = Auth::user();
        $role = new Role;
        $role->role_name = $request->role_name;
        $role->role_description = $request->role_description;
        if(isset($user->company_id)){ $role->company_id = $user->company_id;} else { $role->system_item = 1;};
        $role->author_id = $user->id;
        $role->save();
        if($role){
            $right_role = new RightRole;
            $right_role->role_id = $role->id;
            $right_id = Right::whereRight_action($request->department_id)->first();
            $right_role->right_id = $right_id->id;
            $right_role ->save();

        } else {abort(403);}


        return redirect('/roles');
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
        $user = Auth::user();
        $departments_list = Department::where('company_id', $user->company_id)->whereFilial_status(1)->pluck('department_name', 'id');
        // $this->authorize('update', $entity);

        $menu = Page::get();
        return view('roles.show', compact('role', 'menu', 'departments_list'));
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

    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function setting($id)
    {
        $menu = Page::get();
        $entities = Entity::paginate(30);
        $role = Role::with(['rights' => function($q)
        {
            $q->where('category_right_id', 1);
        }])->findOrFail($id);

        $actions = Action::get();

    // $user = User::findOrFail(Auth::user()->id);

    foreach (Auth::user()->staff as $staffer) {
         dd($staffer->filial_id);
    }
   

        // return view('roles.setting', compact('role', 'menu', 'entities', 'actions'));
    }
}
