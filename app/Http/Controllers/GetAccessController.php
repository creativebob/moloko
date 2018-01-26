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
        $filial_rights = null;
        $filial_id = null;

        // //Получаем права всех должностей
        // $mymass = [];
        // foreach ($user->staff as $staffer) {
        //     $mymass[] = $staffer->filial_id;
        // }
        $departments = Department::whereCompany_id($user->company_id)->get();
        $user = User::with(['staff', 'roles', 'roles.rights', 'roles.rights.actionentity', 'booklists', 'booklists.list_items', 'company'])->findOrFail($user->id);

        //Получаем права первой должности
        if(($user->god == null)&&(isset($user->company_id))){

            // $staffer = $user->staff->first();

            // // Если нет должности - иди нахуй!
            // if(isset($staffer)){

            //     $user_company_id = $user->company_id;
            //     $user_filial_id = $staffer->filial_id; 
            //     $user_department_id = $staffer->department_id;

            //     // Получим все права и их ID в массив ?????????????????????????????????????????????????????????
            //     $auth_user_roles = $user->roles->where('department_id', $user_filial_id);
            //     if(!isset($auth_user_roles)){abort(403, 'Пользователю не назначены права (роли)');};
            //     } else {
            //         abort(403, 'Вы не трудоустроены!');
            //     };

                // ПОЛУЧАЕМ АВТОРОВ ---------------------------------------------------------------------------------------
                // Если есть списки авторов, то указываем их

                if(count($user->booklists) > 0){
                    foreach ($user->booklists as $booklist) {
                        foreach ($booklist->list_items as $list_item) {
                            $list_authors[] = $list_item->item_entity;
                        };
                    };
                } else {$list_authors = null;};

                // -------------------------------------------------------------------------------------------------------

                foreach($user->roles as $role) {
                    foreach($role->rights as $right){
                        $all_rights[$right->alias_right]['right_id'] = $right->id;

                        // Собираем из всех ролей отделы и формируем их список к текущему праву
                        $all_rights[$right->alias_right]['departmets'][$role->pivot->department_id] = $departments->where('id', $role->pivot->department_id)->first()->department_name;

                        // Собираем из всех ролей филиалы и формируем их список к текущему праву
                        if($departments->where('id', $role->pivot->department_id)->first()->filial_status == 1){
                        $all_rights[$right->alias_right]['filials'][$role->pivot->department_id] = $departments->where('id', $role->pivot->department_id)->first()->department_name;};
                    }
                }

        } else {

        // ЕСЛИ БОГ ------------------------------------------------------------------------------------------
            
            $rights = Right::all();

            foreach($rights as $right){

                // Пишем богу все права
                $all_rights[$right->alias_right]['right_id'] = $right->id;

            };

            // Создаем для бога список всех филиалов
            foreach($departments as $department){

                $access['list_departments'][$department->id] = $department->department_name;

                if($department->filial_status == 1){$access['list_departments'][$department->id] = $department->department_name;};
            }

        };


        // ФОРМИРУЕМ МАССИВ С ПРАВАМИ

        // $access['filial_rights'] = $filial_rights;
        $access['all_rights'] = $all_rights;
        $access['user_info']['user_id'] = $user->id;
        $access['user_info']['user_status'] = $user->god;
        $access['user_info']['company_id'] = $user->company_id;
        $access['user_info']['filial_id'] = $user_filial_id;
        $access['user_info']['department_id'] = $user_department_id;

        if($user->company != null){
            $access['company_info']['company_id'] = $user->company->company_id;
            $access['company_info']['company_name'] = $user->company->company_name;
        } else {
            $access['company_info']['company_id'] = '';
            $access['company_info']['company_name'] = '';
        };

        // Пишем в сессию массив с полученными правами!
        session(['access' => $access]);

        // dd($access);
    
        $link = 'users.index';

        if(isset($request->link)){$link = $request->link;};
        return redirect()->route($link);
    }
}
