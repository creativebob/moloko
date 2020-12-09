<?php

namespace App\Http\Controllers;

use App\Client;
use App\Http\Controllers\System\Traits\Locationable;
use App\Http\Controllers\System\Traits\Phonable;
use App\Http\Controllers\System\Traits\Subscriberable;
use App\Http\Controllers\System\Traits\Userable;
use App\Http\Controllers\Traits\Photable;
use App\Http\Requests\System\UserRequest;
use App\Site;
use App\User;
use App\RoleUser;
use App\Role;
use App\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{

    protected $entityAlias;
    protected $entityDependence;

    /**
     * UserController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->entityAlias = 'users';
        $this->entityDependence = true;
    }

    use Locationable,
        Phonable,
        Photable,
        Userable,
        Subscriberable;

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param $siteId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request, $siteId)
    {
        // Включение контроля активного фильтра
        $filter_url = autoFilter($request, $this->entityAlias);
        if(($filter_url != null)&&($request->filter != 'active')){return Redirect($filter_url);};


        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), User::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        // --------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // --------------------------------------------------------------------------------------------------------
        $users = User::with([
            'roles',
            'staff',
            'staff.position',
            'main_phones'
        ])
            ->where('site_id', $siteId)
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->filials($answer) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        // ->where('id', '!=', $request->user()->id) // Только для сущности USERS
        // ->orWhere('id', $request->user()->id) // Только для сущности USERS
//        ->filter($request, 'city_id', 'location')
        ->booleanArrayFilter($request, 'user_type')
        ->booleanArrayFilter($request, 'access_block')
        ->booklistFilter($request)
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->paginate(30);

        // -----------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ------------------------------------------------------------------------------
        // -----------------------------------------------------------------------------------------------------------

        $filter = setFilter($this->entityAlias, $request, [
            'city',                 // Город
            'user_type',            // Свой - чужой
            'access_block',         // Доступ
            'booklist'              // Списки пользователя
        ]);

        // Окончание фильтра -----------------------------------------------------------------------------------------

        $site = Site::find($siteId);

        // Инфо о странице
        $pageInfo = pageInfo($this->entityAlias);

        return view('system.pages.marketings.users.index', compact('users', 'site', 'pageInfo', 'filter'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param $siteId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create($siteId)
    {
        // Подключение политики
        $this->authorize(__FUNCTION__, User::class);

        $user = User::make();

        $site = Site::find($siteId);

        // Инфо о странице
        $pageInfo = pageInfo($this->entityAlias);

        return view('system.pages.marketings.users.create', compact('user', 'site', 'pageInfo'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param UserRequest $request
     * @param $siteId
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(UserRequest $request, $siteId)
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), User::class);

        // TODO - 16.09.20 - Обсудить, коммент к карточке
        $res = $this->checkUserByPhone($this->entityAlias, $siteId);
        if ($res) {
            return back()
                ->withErrors(['msg' => 'Пользователь уже существует']);
        }

        $user = $this->storeUser();

        if (isset($user->email)) {
            $this->storeSubscriber($user);
        }

        if ($request->is_client == 1) {
            $client = Client::create([
                'clientable_id' => $user->id,
                'clientable_type' => 'App\User',
            ]);
        }

        return redirect()->route('users.index', $siteId);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $siteId
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($siteId, $id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $user = User::with([
            'photo',
            'main_phones',
            'extra_phones',
            'location.city'
        ])
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->filials($answer) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->find($id);
        // dd($user);

        if (empty($user)) {
            abort(403,__('errors.not_found'));
        }

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $user);

        $site = Site::find($siteId);

        // Инфо о странице
        $pageInfo = pageInfo($this->entityAlias);

        return view('system.pages.marketings.users.edit', compact('user', 'site', 'pageInfo'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UserRequest $request
     * @param $siteId
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UserRequest $request, $siteId, $id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $user = User::with([
            'location.city',
            'company',
            'photo'
        ])
        ->companiesLimit($answer)
        ->filials($answer) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->moderatorLimit($answer)
        ->find($id);
        // dd($user);

        if (empty($user)) {
            abort(403,__('errors.not_found'));
        }

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $user);

        $this->updateSubscriber($user);

        $user = $this->updateUser($user);

//        $backroute = $request->backroute;
//
//        if(isset($backroute)){
//            return redirect($backroute);
//        };

        return redirect()->route('users.index', $siteId);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $siteId
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($siteId, $id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $user = User::moderatorLimit($answer)
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->filials($answer) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->find($id);

        if (empty($user)) {
            abort(403,__('errors.not_found'));
        }

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $user);

       $res = $user->delete();

        $this->archiveSubscriber($user);

        if (!$res) {
            abort(403,__('errors.destroy'));
        }
        return redirect()->route('users.index', $siteId);
    }


    // --------------------------------------------------------------------------------------------------------------------------
    // СПЕЦИФИЧЕСКИЕ МЕТОДЫ СУЩНОСТИ
    // --------------------------------------------------------------------------------------------------------------------------

    public function getauthcompany($company_id)
    {

        // Только для бога
        $this->authorize('god', User::class);

        $auth_user = User::find(Auth::user()->id);
        $auth_user->company_id = $company_id;
        $auth_user->save();
        return redirect()->route('getaccess.set');
        // return redirect('/getaccess');
    }

    public function getauthuser($user_id)
    {

        // Только для бога
        $this->authorize('god', User::class);
        session(['god' => Auth::user()->id]);

        Auth::loginUsingId($user_id);

        // return redirect('/getaccess');
        return redirect()->route('getaccess.set');
    }

    /**
     * Авторизация на проекте под клиентом
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function authOnProject($id)
    {
        auth()->loginUsingId($id);
        return redirect()->route('project.user.edit');
    }

    public function getgod()
    {
        // Только для бога
        $this->authorize('god', User::class);

        $user = User::find(Auth::user()->id);
        $user->company_id = null;
        $user->save();

        // return redirect('/getaccess');
        return redirect()->route('getaccess.set');
    }

    public function returngod(Request $request)
    {

        if ($request->session()->has('god')) {

            $god_id = $request->session()->get('god');
            $request->session()->forget('god');
            Auth::loginUsingId($god_id);
        }

        return redirect()->route('getaccess.set');
        // return redirect('/getaccess');
    }

    public function profile(Request $request)
    {

        $id = Auth::user()->id;

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $user = User::with('location.city', 'roles', 'role_user', 'role_user.role', 'role_user.position', 'role_user.department', 'photo', 'staff.position.notifications')->find($id);
        // dd($user-Ю);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $user);

        // Функция из Helper отдает массив со списками для SELECT
        $departments_list = getLS('users', 'index', 'departments');
        $filials_list = getLS('users', 'index', 'filials');

        $role = new Role;

        $answer_roles = operator_right('roles', false, 'index');

        $roles_list = Role::moderatorLimit($answer_roles)
        ->companiesLimit($answer_roles)
        ->filials($answer_roles) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
        ->authors($answer_roles)
        ->systemItem($answer_roles) // Фильтр по системным записям
        ->template($answer_roles) // Выводим шаблоны в список
        ->pluck('name', 'id');

        // Получаем список стран
        $countries_list = Country::get()->pluck('name', 'id');

        // Инфо о странице
        $pageInfo = pageInfo($this->entityAlias);
//         dd($user);

        return view('users.profile', compact('user', 'role', 'role_users', 'roles_list', 'departments_list', 'filials_list', 'pageInfo', 'countries_list'));
    }

    public function update_profile(Request $request)
    {


        // Получаем авторизованного пользователя
        $user_auth = $request->user();

        $user_auth_id = hideGod($user_auth);

        $company_id = $user_auth->company_id;

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        // $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $user = $user_auth;

        $filial_id = $request->filial_id;

        // Подключение политики
        // $this->authorize(getmethod(__FUNCTION__), $user);

        // Обновляем локацию
        $user = update_location($request, $user);

        $user->nickname = $request->nickname;

        $user->first_name = $request->first_name;
        $user->second_name = $request->second_name;
        $user->patronymic = $request->patronymic;
        $user->gender = $request->gender;
        $user->birthday_date = $request->birthday_date;

        // Телефон
        $phones = add_phones($request, $user);

        $user->telegram = $request->telegram;

        // $user->orgform_status = $request->orgform_status;

        // $user->user_inn = $request->inn;

        // $user->passport_address = $request->passport_address;
        // $user->passport_number = $request->passport_number;
        // $user->passport_released = $request->passport_released;
        // $user->passport_date = $request->passport_date;

        $user->about = $request->about;
        $user->specialty = $request->specialty;
        $user->degree = $request->degree;
        $user->quote = $request->quote;

        // $user->user_type = $request->user_type;
        // $user->lead_id = $request->lead_id;
        // $user->employee_id = $request->employee_id;
        // $user->access_block = $request->access_block;

        // $user->filial_id = $request->filial_id;
        $user->photo_id = $this->getPhotoId($user);

       $user->save();

       if ($user) {
           // Смотрим обязанности
            if (isset($request->notifications)) {
                $user->notifications()->sync($request->notifications);
            } else {

                // Если удалили последнюю обязанность для должности и пришел пустой массив
                $user->notifications()->detach();
            }
       }



        // Выполняем, только если данные пришли не из userfrofile!
       if(!isset($request->users_edit_mode)){

            // Тут вписываем изменения по правам
        if (isset($request->access)) {

            $delete = RoleUser::whereUser_id($user->id)->delete();
            $mass = [];
            foreach ($request->access as $string) {

                $item = explode(',', $string);
                if ($item[2] == 'null') {
                    $position = null;
                } else {
                    $position = $item[2];
                }

                $mass[] = [
                    'role_id' => $item[0],
                    'department_id' => $item[1],
                    'user_id' => $user->id,
                    'position_id' => $position,
                ];
            }

            DB::table('role_user')->insert($mass);

        } else {

                // Если удалили последнюю роль для должности и пришел пустой массив
            $delete = RoleUser::whereUser_id($user->id)->delete();
        }

    };

    if ($user) {

        return redirect()
            ->route('users.profile')
            ->with(['success' => 'Профиль успешно обновлен']);

            // $backroute = $request->backroute;
            // if(isset($backroute)){
            //     // return redirect()->back();
            //     return redirect($backroute);
            // };

//        return redirect()->route('users.profile');

    } else {
        return back()
            ->withErrors(['msg' => 'Ошибка обновления профиля'])
            ->withInput();
//        abort(403, 'Ошибка при обновлении пользователя!');
    }

}
}
