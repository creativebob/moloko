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

        $user_filial_id = null;
        $user_department_id = null;
        $auth_user_roles = $user->roles;
        $authors['user_id'] = $user->id;
        $filial_rights = null;
        $filial_id = null;

        // //Получаем права всех должностей
        // $mymass = [];
        // foreach ($user->staff as $staffer) {
        //     $mymass[] = $staffer->filial_id;
        // }

        $user = User::with(['staff', 'roles', 'roles.rights', 'booklists', 'booklists.list_items'])->findOrFail($user->id);

        //Получаем права первой должности
        if(($user->god == null)&&(isset($user->company_id))){

            $staffer = $user->staff->first();

            // Если нет должности - иди нахуй!
            if(isset($staffer)){

                $user_filial_id = $staffer->filial_id; 
                $user_department_id = $staffer->department_id; 
                // dd($user_department_id);

                // Получим все права и их ID в массив
                $auth_user_roles = $user->roles->where('department_id', $user_filial_id);
                if(!isset($auth_user_roles)){abort(403, 'Пользователю не назначены права (роли)');};

            } else {
                abort(403, 'Вы не трудоустроены!');
            };

            $access = [];
            $all_rights = [];
            $filial_rights = [];
            $filial_id = null;

            if(isset($user->company_id)){
                $departments = Department::whereCompany_id($user->company_id)->where('filial_status', 1)->get();
            };

            // ПОЛУЧАЕМ АВТОРОВ ---------------------------------------------------------------------------------------
            // Если есть списки авторов, то указываем их

            // dd($user);

            if(count($user->booklists) > 0){

                foreach ($user->booklists as $booklist) {
                    $list_authors = $booklist->list_items->implode('item_entity', ', ');
                };

                $authors['authors_id'] = [$list_authors];

            } else {
                $authors['authors_id'] = null;
            };


            // -------------------------------------------------------------------------------------------------------


        } else {

            // ЕСЛИ БОГ ------------------------------------------------------------------------------------------

        }

        foreach ($user->roles as $role) {
            $department_id = $role->pivot->department_id;

            foreach ($role->rights as $right){

                // Создаем ассоциированный массив прав на авторизованного пользователя
                    $all_rights[$right->actionentity->alias_action_entity . "-" . $right->directive] = $right->id;
                    $item_filial_rights[$right->actionentity->alias_action_entity . "-" . $right->directive] = $right->id;
            }

            // Если не бог - получаем ID филиала
            if($user->god == null){
                if(isset($department_id)){
                    $filial_id = $departments->where('id', $department_id)->first()->filial_id;
                    if($filial_id == null){
                        $filial_id = $department_id;
                    };
                } else {
                    $filial_id = null;
                };

                $filial_rights[$department_id] = $item_filial_rights;   
                             
            };


            $filial_rights[$department_id]['filial'] = $filial_id;
        }

        if(count($filial_rights) == 0){
            abort(403, 'Прав связанных с филиалом не обнаружено');
        };

        $access['filial_rights'] = $filial_rights;
        $access['all_rights'] = $all_rights;
        $access['user_info']['filial_id'] = $user_filial_id;
        $access['user_info']['department_id'] = $user_department_id;
        $access['list_authors'] = $authors;

        // Пишем в сессию массив с полученными правами!
        session(['access' => $access]);
        
        return redirect()->route('users.index');
    }
}
