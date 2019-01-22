<?php

namespace App\Http\Controllers;

// Модели
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
use App\Http\Requests\UserRequest;

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
    protected $entity_name = 'users';
    protected $entity_dependence = true;

    use UserControllerTrait;

    public function index(Request $request)
    {

        // Включение контроля активного фильтра
        $filter_url = autoFilter($request, $this->entity_name);
        if(($filter_url != null)&&($request->filter != 'active')){return Redirect($filter_url);};

        $user_auth = $request->user();

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), User::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));
        // dd($answer);

        // --------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // --------------------------------------------------------------------------------------------------------

        $users = User::with('roles', 'staff', 'staff.position', 'main_phones')
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->filials($answer) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        // ->where('id', '!=', $request->user()->id) // Только для сущности USERS
        // ->orWhere('id', $request->user()->id) // Только для сущности USERS
        ->filter($request, 'city_id', 'location')
        ->booklistFilter($request)
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->paginate(30);

        // -----------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ------------------------------------------------------------------------------
        // -----------------------------------------------------------------------------------------------------------

        $filter = setFilter($this->entity_name, $request, [
            'city',                 // Город
            'booklist'              // Списки пользователя
        ]);

        // Окончание фильтра -----------------------------------------------------------------------------------------

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        return view('users.index', compact('users', 'page_info', 'filter', 'user'));
    }

    public function create(Request $request)
    {

        $user_auth = $request->user();

        // Подключение политики
        $this->authorize(__FUNCTION__, User::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        $user = new User;

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        return view('users.create', compact('user', 'page_info'));
    }

    public function store(UserRequest $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), User::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));


        // ПОЛУЧЕНИЕ И СОХРАНЕНИЕ ДАННЫХ
        $new_user = new User;

        // Отдаем работу по созданию нового юзера трейту
        $new_user = $this->createUser($request, $new_user);

        return Redirect('/admin/users');

    }

    public function show(Request $request, $id)
    {


    }

    public function edit(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $user = User::with(
            'location.city',
            'photo',
            'main_phones',
            'extra_phones'
        )->moderatorLimit($answer)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $user);

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        return view('users.edit', compact('user', 'page_info'));
    }

    public function update(UserRequest $request, $id)
    {
        // Получаем авторизованного пользователя
        $user_auth = $request->user();

        $user_auth_id = hideGod($user_auth);

        $company_id = $user_auth->company_id;

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $user = User::with('location', 'company', 'photo')->moderatorLimit($answer)->findOrFail($id);

        $filial_id = $request->filial_id;

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $user);

        // Отдаем работу по созданию нового юзера трейту
        $user = $this->updateUser($request, $user);

        $backroute = $request->backroute;

        if(isset($backroute)){
            return redirect($backroute);
        };

        return redirect('/admin/users');

    }

    public function destroy(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $user = User::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $user);

        // Удаляем пользователя с обновлением
        $user = User::destroy($id);

        if($user) {return redirect('/admin/users');} else {abort(403,'Что-то пошло не так!');};
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


    public function myprofile(Request $request)
    {

        $id = Auth::user()->id;

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

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
        $page_info = pageInfo($this->entity_name);
        // dd($user);

        return view('users.myprofile', compact('user', 'role', 'role_users', 'roles_list', 'departments_list', 'filials_list', 'page_info', 'countries_list'));
    }


    public function updatemyprofile(Request $request)
    {


        // Получаем авторизованного пользователя
        $user_auth = $request->user();

        $user_auth_id = hideGod($user_auth);

        $company_id = $user_auth->company_id;

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        // $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

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

        $user->telegram_id = $request->telegram_id;

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


        // Если прикрепили фото
        if ($request->hasFile('photo')) {

            // Вытаскиваем настройки
            // Вытаскиваем базовые настройки сохранения фото
            $settings = config()->get('settings');

            // Начинаем проверку настроек, от компании до альбома
            // Смотрим общие настройки для сущности
            $get_settings = PhotoSetting::where(['entity' => $this->entity_name])->first();

            if($get_settings){

                if ($get_settings->img_small_width != null) {
                    $settings['img_small_width'] = $get_settings->img_small_width;
                }

                if ($get_settings->img_small_height != null) {
                    $settings['img_small_height'] = $get_settings->img_small_height;
                }

                if ($get_settings->img_medium_width != null) {
                    $settings['img_medium_width'] = $get_settings->img_medium_width;
                }

                if ($get_settings->img_medium_height != null) {
                    $settings['img_medium_height'] = $get_settings->img_medium_height;
                }

                if ($get_settings->img_large_width != null) {
                    $settings['img_large_width'] = $get_settings->img_large_width;
                }

                if ($get_settings->img_large_height != null) {
                    $settings['img_large_height'] = $get_settings->img_large_height;
                }

                if ($get_settings->img_formats != null) {
                    $settings['img_formats'] = $get_settings->img_formats;
                }

                if ($get_settings->img_min_width != null) {
                    $settings['img_min_width'] = $get_settings->img_min_width;
                }

                if ($get_settings->img_min_height != null) {
                    $settings['img_min_height'] = $get_settings->img_min_height;
                }

                if ($get_settings->img_max_size != null) {
                    $settings['img_max_size'] = $get_settings->img_max_size;

                }
            }



            // dd($company_id);
            // Директория
            $directory = $user->company_id.'/media/users/'.$user->id.'/img';

            // Отправляем на хелпер request(в нем находится фото и все его параметры (так же id автора и id сомпании), директорию сохранения, название фото, id (если обновляем)), настройки, в ответ придет МАССИВ с записаным обьектом фото, и результатом записи
            if ($user->photo_id) {
                $array = save_photo($request, $directory, 'avatar-'.time(), null, $user->photo_id, $settings);

            } else {
               $array = save_photo($request, $directory, 'avatar-'.time(), null, null, $settings);
           }

           $photo = $array['photo'];

           $user->photo_id = $photo->id;
       }

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

            // $backroute = $request->backroute;
            // if(isset($backroute)){
            //     // return redirect()->back();
            //     return redirect($backroute);
            // };

        return redirect('/admin/home');

    } else {
        abort(403, 'Ошибка при обновлении пользователя!');
    }

}
}
