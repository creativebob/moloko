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
use App\ListUser;
use App\Booklist;
use App\List_item;

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

        $user = Auth::user();

        // //Получаем права всех должностей
        // $mymass = [];
        // foreach ($user->staff as $staffer) {
        //     $mymass[] = $staffer->filial_id;
        // }

        $user = User::with(['staff', 'roles', 'roles.rights', 'booklists', 'booklists.list_items'])->findOrFail($user->id);


        //Получаем права первой должности
        if($user->god == null){

            $staffer = $user->staff->first();

            // Если нет должности - иди нахуй!
            if(isset($staffer)){

                $user_filial_id = $staffer->filial_id; 
                $user_department_id = $staffer->department_id; 
                // dd($user_department_id);

                // Получим все права и их ID в массив
                $auth_user_roles = $user->roles->where('department_id', $user_filial_id);


            } else {
                abort(403, 'Вы не трудоустроены!');
            };

        } else {

            //Если бог
            $user_filial_id = null;
            $user_department_id = null;
            $auth_user_roles = $user->roles;
        }

        if(!isset($auth_user_roles)){abort(403);};

        $access = [];
        $all_rights = [];
        $filial_rights = [];
        $filial_id = null;


        if(isset($user->company_id)){
            $departments = Department::whereCompany_id($user->company_id)->where('filial_status', 1)->get();
        };

        foreach ($user->roles as $role) {

            $department_id = $role->pivot->department_id;
            // Пытаюсь получить ID филиала



            // if(isset($user->company_id)){

            //     $filial_id = $departments->where('id', $department_id)->first()->filial_id;
            //     dd($filial_id);
            // }

            // dd($filial_id);

            // dd($department_id);
            foreach ($role->rights as $right){

                // Создаем ассоциированный массив прав на авторизованного пользователя
                // В формате: Ключ"user-create-allow" и значение "1" если найдено правило.

                // if(isset($allrights_array[$right->actionentity->alias_action_entity . "-" . 'deny'])){
                    $all_rights[$right->actionentity->alias_action_entity . "-" . $right->directive] = $right->id;
                    $item_filial_rights[$right->actionentity->alias_action_entity . "-" . $right->directive] = $right->id;
                // };          
            }


            $filial_rights[$department_id] = $item_filial_rights;
            // $filial_rights[$department_id]['filial'] = $filial_id;
        }

        if(count($filial_rights) == 0){
            abort(403);
        };

        $list_authors = [];
        foreach ($user->booklists as $booklist) {
            $list_authors = $booklist->list_items->implode('item_entity', ', ');
            
            // foreach ($booklist->list_items as $list_item) {
            //     $list_authors[] = $list_item->item_entity;
            // }
        }

        // Если есть списки авторов, то указываем их
        if(count($list_authors)>0){$authors['authors_id'] = [$list_authors];} else {$authors['authors_id'] = null;};
        $authors['user_id'] = $user->id;

        $access['list_authors'] = $authors;
        $access['filial_rights'] = $filial_rights;
        $access['all_rights'] = $all_rights;
        $access['user_info']['filial_id'] = $user_filial_id;
        $access['user_info']['department_id'] = $user_department_id;

        // dd($access);

        // $request->session()->put('access', $all_rights);
        session(['access' => $access]);
        
        return redirect()->route('users.index');
    }
}
