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
use App\Position;
use App\Challenge;
use App\CompaniesSetting;


// Модели которые отвечают за работу с правами + политики
use App\Role;
use App\Policies\UserPolicy;
use App\UsersLogin;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

// Запросы и их валидация
use Illuminate\Http\Request;
use App\Http\Requests\System\UpdateUser;

// Прочие необходимые классы
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class GetAccessController extends Controller
{
    /**
     * Вход в систему
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function enter()
    {
        if (auth()->user()) {
            return redirect()->route('dashboard.index');
        } else {
            return view('layouts.enter');
        }
    }

    public function set(Request $request)
    {

        // Ссылка по умолчанию на страницу входа
        $link = 'employees.index';


        // Получаем пользователя в user
        $user = $request->user();

        auth()->user()->logins()->save(UsersLogin::make([
            'logined_at' => now(),
            'ip' => request()->ip()
        ]));

        if ($user->access_block == 1) {
            abort(403, "Сорян, вам сюда нельзя! Блокировочка!");
        };

        // Собираем данные о компании
        if ($user->company_id != null) {


            // Получаем все отделы компании
            $departments = Department::whereCompany_id($user->company_id)->get();

            // Настройка прав бога, если он авторизован под компанией
            foreach ($departments as $department) {

                // Пишем в сессию список отделов
                $access['company_info']['departments'][$department->id] = $department->name;

                // Пишем в сессию список филиалов
                if ($department->parent_id == null) {
                    $access['company_info']['filials'][$department->id] = $department->name;
                };
            }
        };


        // ------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        // РАЗДЕЛ ПРАВ ОБЫЧНОГО КЛИЕНТА КОМПАНИИ ----------------------------------------------------------------------------------------------------------------------------------
        // ------------------------------------------------------------------------------------------------------------------------------------------------------------------------

        // Если пользователь авторизованный посетитель сайта - обычный клиент компании.
        if ($user->user_type == 0) {

            // Пока что гасим его на входе. Это раздел будем пистаь позже...
            abort(403, "Отлично, мы видим, что вы наш авторизованный КЛИЕНТ!");
        };


        // ------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        // РАЗДЕЛ ПРАВ ПОЛЬЗОВАТЕЛЯ CRM SYSTEM ------------------------------------------------------------------------------------------------------------------------------------
        // ------------------------------------------------------------------------------------------------------------------------------------------------------------------------

        // Если пользователь являеться пользователем CRM System.
        if ($user->user_type == 1) {


            // ЕСЛИ ПОЛЬЗОВАТЕЛЬ БОГ:
            if ($user->god == 1) {

                $user_department_id = null;

                $rights = Right::all();
                foreach ($rights as $right) {

                    // Пишем богу все права
                    if ($right->directive == 'allow') {
                        $all_rights[$right->alias_right]['right_id'] = $right->id;
                    };

                };

                if ($user->company_id == null) {

                    // Настройка прав бога, если не определена компания

                } else {


                };

            };


            // ЕСЛИ ПОЛЬЗОВАТЕЛЬ СОТРУДНИК
            if ($user->god == null) {

                // Вырубаемся, если пользователь не имеет ID компании
                if ($user->company_id == null) {
                    abort(403, "Пользователь не имеет связи с компанией");
                };

                // Получаем данные на пользователя
                $user = User::with(['staff', 'staff.position.page', 'roles', 'roles.rights', 'roles.rights.actionentity.entity', 'booklists', 'booklists.list_items', 'company'])->find($user->id);

                // Проверяем, устроен ли пользователь в компании
                $user_department = $user->staff->first();

                if ($user->staff->first() == null) {
                    abort(403, "Пользователь не устроен в компании!");
                };

                $start_url_alias = $user->staff->first()->position->page->alias ?? 'dashboard';
                $user_redirect = '/admin/' . $start_url_alias;
                $user_position_id = $user->staff->first()->position->id;


                // Получаем массив дополнительных прав
                $extra_rights = $user->staff[0]->position->charges->pluck('name', 'alias')->toArray();

                if ($user_department != null) {
                    $user_filial_id = $user_department->filial_id;
                    $user_department_id = $user_department->department_id;
                } else {
                    abort(403, "Пользователь не устроен в компании!");
                };

                // dd($user->filial_id);

                // Вырубаемся, если пользователь не имеет ID компании, филиала или департамента
                if (($user->filial_id == null) || ($user_department_id == null)) {
                    abort(403, "Пользователь не имеет связи с филиалом или должностью");
                };

                // dd($user->staff);

                //Получаем права всех должностей
                // $mymass = [];
                // foreach ($user->staff as $staffer) {
                //     $mymass[] = $staffer->department_id;
                //     $mymass[] = $staffer->filial_id;
                // }

                // ПОЛУЧАЕМ АВТОРОВ ---------------------------------------------------------------------------------------
                // Если есть списки авторов, то указываем их

                if (count($user->booklists) > 0) {
                    foreach ($user->booklists as $booklist) {
                        foreach ($booklist->list_items as $list_item) {
                            $list_authors[] = $list_item->item_entity;
                        };
                    };
                } else {
                    $list_authors = null;
                };

                if (!isset($list_authors)) {
                    $list_authors = null;
                };

                // -------------------------------------------------------------------------------------------------------

                // Получаем все филиалы компании (Будут использоваться при снятии филиалозависимости)
                $all_filials_user = Department::whereCompany_id($user->company_id)->whereNull('filial_id')->pluck('name', 'id')->toArray();
                $allFilialsUser = Department::whereCompany_id($user->company_id)
                    ->whereNull('filial_id')
                    ->get([
                        'id',
                        'name'
                    ]);

                // Формируеться список филиалов и отделов доступных на каждую сущность
                foreach ($user->roles as $role) {
                    foreach ($role->rights as $right) {

                        $all_rights[$right->alias_right]['right_id'] = $right->id;

                        // Собираем из всех ролей отделы и формируем их список к текущему праву
                        $all_rights[$right->alias_right]['departments'][$role->pivot->department_id] = $departments->where('id', $role->pivot->department_id)->first()->name;


                        // При обработке права на просмотр чужих записей добавляем список авторов к праву
                        if ($right->alias_right == 'authors-users-allow') {
                            $all_rights[$right->alias_right]['authors'] = $list_authors;
                        };

                        $nl = $role->rights->where('alias_right', 'nolimit-' . $right->actionentity->entity->alias . '-allow')->count();

                        if ($nl != 0) {

                            // Если существует безлимит на филиалы
                            $all_rights[$right->alias_right]['filials'] = $all_filials_user;

                            // TODO - 15.12.20 - Список филиалов, доступных пользователю
                            $all_rights[$right->alias_right]['filials_for_user'] = $allFilialsUser;
                        } else {

                            // Собираем из всех ролей филиалы и формируем их список к текущему праву
                            if ($departments->where('id', $role->pivot->department_id)->first()->parent_id == null) {
                                $all_rights[$right->alias_right]['filials'][$role->pivot->department_id] = $departments->where('id', $role->pivot->department_id)->first()->name;

                                // TODO - 15.12.20 - Список филиалов, доступных пользователю
                                $object = app()->make('stdClass');
                                $object->id = $departments->where('id', $role->pivot->department_id)->first()->id;
                                $object->name = $departments->where('id', $role->pivot->department_id)->first()->name;
                                $all_rights[$right->alias_right]['filials_for_user'][] = $object;
                            };
                        }

                        // // При обработке права на просмотр чужих записей добавляем список авторов к праву
                        // if($right->alias_right == $right->entity->alias . 'nolimit-allow'){$all_rights[$right->alias_right]['authors'] = $list_authors;};

                    }


                }

                // dd($all_rights);


                if (!isset($all_rights)) {
                    Auth::logout();
                    abort(403, "У пользователя отсутствуют права!");
                };

            };

            // ФОРМИРУЕМ МАССИВ С ПРАВАМИ

            $access['all_rights'] = $all_rights;
            $access['user_info']['user_id'] = $user->id;
            $access['user_info']['user_status'] = $user->god;
            $access['user_info']['company_id'] = $user->company_id;
            $access['user_info']['filial_id'] = $user->filial_id;
            $access['user_info']['department_id'] = $user_department_id;
            if (isset($user_position_id)) {
                $access['user_info']['position_id'] = $user_position_id;
                $access['user_info']['extra_rights'] = $extra_rights;
            }


            // Торговые точки и рабочие места
            if ($user->staff->isNotEmpty()) {
                $workplaces = $user->staff->first()->workplaces;
                $access['user_info']['workplaces'] = $user->staff->first()->workplaces;
                if ($workplaces->isNotEmpty()) {
                    $workplaces->load([
                        'outlet.settings'
                    ]);
                    foreach($workplaces as $workplace) {
                        $outlet = $workplace->outlet;
                        if ($outlet) {
                            $access['user_info']['outlets'][] = (object) [
                                'id' => $outlet->id,
                                'name' => $outlet->name,
                                'filial_id' => $outlet->filial_id,
                                'extra_time' => $outlet->extra_time,
                                'settings' => $outlet->settings
                            ];
                        }
                    }
                } else {
                    $access['user_info']['outlets'] = null;
                }
            }


            // $challenges = Challenge::with(
            //     'author',
            //     'appointed',
            //     'finisher',
            //     'challenges'
            // )
            // ->where('appointed_id', $user->id)
            // ->companiesLimit($user->company_id)
            // ->where('status', null)
            // ->whereDay('deadline_date', Carbon::now()->format('d'))
            // ->orderBy('deadline_date', 'asc')
            // ->orderBy('moderation', 'desc')
            // ->get()
            // ->groupBy(function($challenges) {
            //     return Carbon::parse($challenges->deadline_date)->format('d.m.Y'); // А это то-же поле по нему мы и будем группировать
            // });

            // // dd($challenges);

            // $user_challenges = $challenges;
            // $access['user_info']['challenges'] = $user_challenges;

            if ($user->company != null) {
                $access['company_info']['company_id'] = $user->company_id;
                $access['company_info']['company_name'] = $user->company->name;
                $access['company_info']['company_designation'] = $user->company->designation;
                $access['company_info']['sector_id'] = $user->company->sector_id;
                $access['company_info']['schedule'] = $user->company->schedule;

            } else {
                $access['company_info']['company_designation'] = '';
                $access['company_info']['company_id'] = '';
                $access['company_info']['company_name'] = '';
                $access['company_info']['sector_id'] = '';
            };

            // Пишем в сессию массив с полученными правами!
            session(['access' => $access]);

            // Создаем массив с сущностями к которым разрешен доступ на Index (Для отображения пунктов меню)
            $entities_list = [];


            foreach ($user->roles as $role) {
                foreach ($role->rights as $right) {

                    // Статичное указание ID действия 'index' - 2
                    if ($right->action_id == 2) {
                        if ($user->can('index', $right->actionentity->entity->model)) {
                            $entities_list[] = $right->actionentity->entity->id;
                        };
                    };
                }
            }
//             dd($entities_list);

            $access['settings']['entities_list'] = $entities_list;

            // Пишем настройки пользователя
//            $settings = Setting::where('user_id', $user->id)->get();
//
//            // dd($settings);
//
//            $conditions = [];
//
//            if ($settings) {
//
//                foreach ($settings as $setting) {
//                    $conditions['conditions'][$setting->key] = $setting->value;
//                }
//
//                // dd($user_settings);
//
//
//            }


            // Перезаписываем сессию
            session([
                'access' => $access,
//                'conditions' => $conditions
            ]);


            if (isset($user_redirect)) {
                return redirect($user_redirect);;
            };


            if (isset($request->link)) {
                $link = $request->link;
            };


            if (isset($request->method)) {
                $action_method = $request->method;
            };
            if (isset($request->action_array)) {
                $action_array = $request->action_array;
            };

            // if((isset($action_method))&&(isset($action_method))){

            //     return redirect()->action($action_method, $action_array);

            // } else {
            return redirect()->route($link);
            // };

        };
    }
}
