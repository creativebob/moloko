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

        $mymass = [];

        $user = Auth::user();
        foreach ($user->staff as $staffer) {
            $mymass[] = $staffer->filial_id;
        }


        // Получим все права и их ID в массив
        $auth_user_roles = $user->roles;

        // Находим все возможные в системе права и кладем их в массив с указанием их ID
        // $allrights_array = [];
        // foreach ($auth_user_roles as $role) {
        //     foreach ($role->rights as $right) {

        //         $allrights_array[$right->actionentity->alias_action_entity . "-" . $right->directive] = $right->id;

        //     }
        // }



        // Создаем ассоциированный массив прав на авторизованного пользователя
        // В формате: Ключ"user-create-allow" и значение "1" если найдено правило.
        $user_access = [];
        foreach ($user->roles as $role) {
            foreach ($role->rights as $right){
                
                // if(isset($allrights_array[$right->actionentity->alias_action_entity . "-" . 'deny'])){
                    $user_access[$right->actionentity->alias_action_entity . "-" . $right->directive] = $right->id;
                // };          
            }
        }

        // Получаем авторов
        // TODO
        

        $booklist = Booklist::get();

        // dd($list_users->list_items);


        // foreach ($list_users as $list) {
        //     foreach ($list->list_items as $list_item){
                
         


        //         $authors[] = $list_item->item;
        
        //     }
        // }


        dd($booklist);

        $authors['authors_id'] = [5, 6, 7];
        $authors['user_id'] = $user->id;
        $user_access['list_authors'] = $authors;

        // dd($user_access);

        // $request->session()->put('access', $user_access);
        session(['access' => $user_access]);
        
        return redirect()->route('users.index');
    }
}
