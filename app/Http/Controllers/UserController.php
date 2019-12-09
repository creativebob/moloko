<?php

namespace App\Http\Controllers;

// Модели
use App\Http\Controllers\Traits\Photable;
use App\Site;
use App\User;
use App\Position;
use App\Staffer;
use App\RoleUser;
use App\List_item;
use App\Photo;
use App\Location;
use App\Booklist;
use App\Role;
use App\Country;

// Валидация
use Illuminate\Http\Request;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;

// Политики
use App\Policies\UserPolicy;

// Общие классы
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cookie;

// Специфические классы
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;

// На удаление
use App\Http\Controllers\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Traits\UserControllerTrait;


class UserController extends Controller
{

    // Сущность над которой производит операции контроллер
    protected $entity_alias = 'users';
    protected $entity_dependence = true;

    use UserControllerTrait;
	use Photable;

    public function index(Request $request, $site_id)
    {

        // Включение контроля активного фильтра
        $filter_url = autoFilter($request, $this->entity_alias);
        if(($filter_url != null)&&($request->filter != 'active')){return Redirect($filter_url);};

        $user_auth = $request->user();

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), User::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));
        // dd($answer);

        // --------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // --------------------------------------------------------------------------------------------------------

        $users = User::with('roles', 'staff', 'staff.position', 'main_phones')
            ->where('site_id', $site_id)
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->filials($answer) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        // ->where('id', '!=', $request->user()->id) // Только для сущности USERS
        // ->orWhere('id', $request->user()->id) // Только для сущности USERS
        ->filter($request, 'city_id', 'location')
        ->booleanArrayFilter($request, 'user_type')
        ->booleanArrayFilter($request, 'access_block')
        ->booklistFilter($request)
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->paginate(30);

        // -----------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ------------------------------------------------------------------------------
        // -----------------------------------------------------------------------------------------------------------

        $filter = setFilter($this->entity_alias, $request, [
            'city',                 // Город
            'user_type',            // Свой - чужой
            'access_block',         // Доступ
            'booklist'              // Списки пользователя
        ]);

        // Окончание фильтра -----------------------------------------------------------------------------------------

        // Инфо о странице
        $page_info = pageInfo($this->entity_alias);

        $site = Site::findOrFail($site_id);

        return view('users.index', compact('users', 'page_info', 'filter', 'site_id', 'site'));
    }

    public function create(Request $request, $site_id)
    {

        $user_auth = $request->user();

        // Подключение политики
        $this->authorize(__FUNCTION__, User::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $user = new User;

        // Инфо о странице
        $page_info = pageInfo($this->entity_alias);

        $auth_user = Auth::user();

        $site = Site::findOrFail($site_id);

        return view('users.create', compact('user', 'auth_user', 'page_info', 'site_id', 'site'));
    }

    public function store(UserStoreRequest $request, $site_id)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), User::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ПОЛУЧЕНИЕ И СОХРАНЕНИЕ ДАННЫХ
        // Отдаем работу по созданию нового юзера трейту
        $new_user = $this->createUser($request, $site_id);

        return redirect()->route('users.index', $site_id);

    }

    public function show(Request $request, $id)
    {


    }

    public function edit(Request $request, $site_id, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $user = User::with([
            'photo',
            'main_phones',
            'extra_phones'])
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->filials($answer) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->findOrFail($id);
        // dd($user);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $user);

        // Инфо о странице
        $page_info = pageInfo($this->entity_alias);

        $auth_user = \Auth::user();

        $site = Site::findOrFail($site_id);

        return view('users.edit', compact('user', 'page_info', 'auth_user', 'site_id', 'site'));
    }

    public function update(UserUpdateRequest $request, $site_id, $id)
    {
        // Получаем авторизованного пользователя
        $user_auth = $request->user();

        $user_auth_id = hideGod($user_auth);

        $company_id = $user_auth->company_id;

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $user = User::with('location', 'company', 'photo')
        ->companiesLimit($answer)
        ->filials($answer) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->moderatorLimit($answer)
        ->findOrFail($id);

        // $filial_id = $request->filial_id;

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $user);

        // Отдаем работу по созданию нового юзера трейту
        $user = $this->updateUser($request, $user);

        $backroute = $request->backroute;

        if(isset($backroute)){
            return redirect($backroute);
        };

        return redirect()->route('users.index', $site_id);

    }

    public function destroy(Request $request, $site_id, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $user = User::moderatorLimit($answer)
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->filials($answer) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $user);

        // Удаляем пользователя с обновлением
        $user = User::destroy($id);

        if($user) {
            return redirect()->route('users.index', $site_id);
        } else {
            abort(403,'Что-то пошло не так!');
        };
    }


    // --------------------------------------------------------------------------------------------------------------------------
    // СПЕЦИФИЧЕСКИЕ МЕТОДЫ СУЩНОСТИ
    // --------------------------------------------------------------------------------------------------------------------------

    public function getauthcompany($company_id)
    {

        // Только для бога
        $this->authorize('god', User::class);

        $auth_user = User::findOrFail(Auth::user()->id);
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

    public function getgod()
    {
        // Только для бога
        $this->authorize('god', User::class);

        $user = User::findOrFail(Auth::user()->id);
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
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $user = User::with('location.city', 'roles', 'role_user', 'role_user.role', 'role_user.position', 'role_user.department', 'photo', 'staff.position.notifications')->findOrFail($id);
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
        $page_info = pageInfo($this->entity_alias);
//         dd($user);

        return view('users.profile', compact('user', 'role', 'role_users', 'roles_list', 'departments_list', 'filials_list', 'page_info', 'countries_list'));
    }


    public function update_profile(Request $request)
    {


        // Получаем авторизованного пользователя
        $user_auth = $request->user();

        $user_auth_id = hideGod($user_auth);

        $company_id = $user_auth->company_id;

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        // $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

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
        $user->sex = $request->sex;
        $user->birthday = $request->birthday;

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
        $user->photo_id = $this->getPhotoId($request, $user);

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
