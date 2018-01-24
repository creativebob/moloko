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
                $departments = Department::whereCompany_id($user->company_id)->get();
            };


            // ПОЛУЧАЕМ АВТОРОВ ---------------------------------------------------------------------------------------
            // Если есть списки авторов, то указываем их

            // dd($user);

            if(count($user->booklists) > 0){

                foreach ($user->booklists as $booklist) {
                    foreach ($booklist->list_items as $list_item) {
                        $list_authors[] = $list_item->item_entity;
                    };
                };

                // dd($list_authors);

            } else {

                $list_authors = null;
            };


            // -------------------------------------------------------------------------------------------------------


        $right_mass = [];

        foreach($user->roles as $role) {

            $department = $departments->where('id', $role->pivot->department_id)->first();
            $department_id = $department->id;
            $department_name = $department->department_name;
            $filial_status = $department->filial_status;

            if($filial_status == 1){

                $filial_id = $department->id;
                $filial_name = $department_name;

            } else {

                $filial_id = $departments->where('id', $department_id)->first()->filial_id;
                $filial_name = $departments->where('id', $department_id)->first()->department_name;
            };


            foreach($role->rights as $right){

                $j = $role->rights->where('alias_right', $right->actionentity->alias_action_entity . "-deny")->count();
                if($j > 0){


                } else {

                    // СОЗДАЕМ ГЛАНЫЙ МАССИВ ДЛЯ СЕССИИ:
                    // В массив пришем ID права
                    $all_rights[$right->actionentity->alias_action_entity . "-" . $right->directive]['right_id'] = $right->id;

                    $item_filial_rights[$right->actionentity->alias_action_entity . "-" . $right->directive] = $right->id;

                    // К праву смотреть чужих авторов - пишем список авторов
                    if($right->actionentity->alias_action_entity . "-" . $right->directive == 'authors-users-allow')
                    {
                        $all_rights[$right->actionentity->alias_action_entity . "-" . $right->directive]['authors'] = $list_authors;
                    } else {
                        $all_rights[$right->actionentity->alias_action_entity . "-" . $right->directive]['authors'] = null;
                    };


                    // Пишем ID департамента, в ключе указываем ID филиала
                    $all_rights[$right->actionentity->alias_action_entity . "-" . $right->directive]['departments'][$filial_id] = $department_id;

                    if(($right->actionentity->action_id == 3)||($right->actionentity->action_id == 4)){

                        if($filial_status == 1){$all_rights[$right->actionentity->alias_action_entity . "-" . $right->directive]['list_filials'][$filial_id] = $filial_name;};
                        $all_rights[$right->actionentity->alias_action_entity . "-" . $right->directive]['list_departments'][$department_id] = $department_name; 
                    };


                    if(isset($all_rights[$right->actionentity->alias_action_entity . "-deny"]['departments'][$filial_id][$department_id])){

                        unset($all_rights[$right->actionentity->alias_action_entity . "-allow"]['departments'][$filial_id]);

                        if($filial_status == 1){
                            unset($all_rights[$right->actionentity->alias_action_entity . "-" . $right->directive]['list_departments'][$department_id]);
                            unset($all_rights[$right->actionentity->alias_action_entity . "-" . $right->directive]['list_filials'][$filial_id]);
                        };
                    };


                };

            };

            // Если не бог - получаем ID филиала
            if($user->god == null){

                if((isset($department_id))&&($department_id !== null)){

                    $filial_id = $departments->where('id', $department_id)->first()->filial_id;
 
                    // dd($filial_id);

                } else {
                    $filial_id = null;
                };

                $filial_rights[$department_id] = $item_filial_rights;
                             
            };

            $filial_rights[$department_id]['filial'] = $filial_id;
        };

        if(count($filial_rights) == 0){
            abort(403, 'Прав связанных с филиалом не обнаружено');
        };



        } else {

            // ЕСЛИ БОГ ------------------------------------------------------------------------------------------
            foreach($user->roles as $role) {
                foreach($role->rights as $right){

                    // Пишем богу все права
                    $all_rights[$right->actionentity->alias_action_entity . "-" . $right->directive] = $right->id;
                    $right_mass['right_id'] = $right->id;
                    $all_rights[$right->actionentity->alias_action_entity . "-" . $right->directive] = $right_mass;

                };
            };

            $user_filial_id = null;
            $user_department_id = null;

        };


        // ФОРМИРУЕМ МАССИВ С ПРАВАМИ

        $access['filial_rights'] = $filial_rights;
        $access['all_rights'] = $all_rights;
        $access['user_info']['user_id'] = $user->id;
        $access['user_info']['user_status'] = $user->god;
        $access['user_info']['company_id'] = $user->company_id;
        $access['user_info']['filial_id'] = $user_filial_id;
        $access['user_info']['department_id'] = $user_department_id;

        // Пишем в сессию массив с полученными правами!
        session(['access' => $access]);
        

        $link = 'users.index';

        if(isset($request->link)){$link = $request->link;};
        return redirect()->route($link);
    }
}
