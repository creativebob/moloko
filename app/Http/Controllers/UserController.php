<?php

namespace App\Http\Controllers;

use App\User;
use App\Position;
use App\Staffer;
use App\RoleUser;
use App\List_item;
use App\Booklist;
use App\Photo;
use App\Location;

use App\Http\Controllers\Session;

// Модели которые отвечают за работу с правами + политики
use App\Role;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

// Запросы и их валидация
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;

// Прочие необходимые классы
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

use Intervention\Image\ImageManagerStatic as Image;

class UserController extends Controller
{

    // Сущность над которой производит операции контроллер
  protected $entity_name = 'users';
  protected $entity_dependence = true;

  public function index(Request $request)
  {

        // Подключение политики
    $this->authorize(getmethod(__FUNCTION__), User::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));
        // dd($answer);

        // ---------------------------------------------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // ---------------------------------------------------------------------------------------------------------------------------------------------

    $users = User::with('roles', 'staff', 'staff.position')  
    ->moderatorLimit($answer)
    ->companiesLimit($answer)
        ->filials($answer) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям              
        ->orWhere('id', $request->user()->id) // Только для сущности USERS
        ->filter($request, 'city', 'location')
        ->booklistFilter($request)
        ->orderBy('moderation', 'desc')
        ->paginate(30);


        // Запрос для фильтра
        $filter_query = User::with('location.city')
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->filials($answer) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям              
        ->orWhere('id', $request->user()->id) // Только для сущности USERS
        ->get();

        $filter['status'] = null;

        // Перечень подключаемых фильтров:
        $filter = addFilter($filter, $filter_query, $request, 'Выберите город:', 'city', 'city_id', 'location');

        // Добавляем данные по спискам (Требуется на каждом контроллере)
        $filter = addBooklist($filter, $filter_query, $request, $this->entity_name);

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        $user_auth = $request->user();

        // dd(Storage::disk('public')->get($user_auth->photo));


            // dd(public_path('app/public/1/1/1/3/3/3'));
        // $ava = Storage::disk('public')->get(storage_path('app\public'.$user_auth->photo));
        // dd($ava);

        // dd(storage_path('app\public'));

        // dd(Storage::disk('public')->url($user_auth->photo));
        // 

        return view('users.index', compact('users', 'page_info', 'filter', 'user'));
      }

      public function create(Request $request)
      {

        $user_auth = $request->user();

        // Подключение политики
        $this->authorize(__FUNCTION__, User::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // Функция из Helper отдает массив со списками для SELECT
        $departments_list = getLS('users', 'view', 'departments');
        $filials_list = getLS('users', 'view', 'departments');

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_roles = operator_right('roles', false, 'index');
        $roles_list = Role::whereCompany_id($user_auth->company_id)->moderatorLimit($answer_roles)->pluck('name', 'id');

        $user = new User;
        $roles = new Role;

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        return view('users.create', compact('user', 'roles', 'filials_list', 'departments_list', 'roles_list', 'page_info'));
      }

      public function store(UserRequest $request)
      {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), User::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // Получаем данные для авторизованного пользователя
        $user_auth = $request->user();
        $user_auth_id = hideGod($user_auth);
        $user_status = $user_auth->god;
        $company_id = $user_auth->company_id;
        $filial_id = $request->filial_id;


        // Пишем локацию
        $location = new Location;
        $location->city_id = $request->city_id;
        $location->address = $request->address;
        $location->author_id = $user_auth_id;
        $location->save();

        if ($location) {
          $location_id = $location->id;
        } else {
          abort(403, 'Ошибка записи адреса');
        }

        // ПОЛУЧЕНИЕ И СОХРАНЕНИЕ ДАННЫХ
        $user = new User;

        $user->login = $request->login;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->nickname = $request->nickname;

        $user->first_name =   $request->first_name;
        $user->second_name = $request->second_name;
        $user->patronymic = $request->patronymic;
        $user->sex = $request->sex;
        $user->birthday = $request->birthday;

        $user->phone = cleanPhone($request->phone);

        if(($request->extra_phone != Null)&&($request->extra_phone != "")){
          $user->extra_phone = cleanPhone($request->extra_phone);
        };

        $user->telegram_id = $request->telegram_id;
        $user->location_id = $location_id;

        $user->orgform_status = $request->orgform_status;
        $user->user_inn = $request->inn;

        $user->passport_address = $request->passport_address;
        $user->passport_number = $request->passport_number;
        $user->passport_released = $request->passport_released;
        $user->passport_date = $request->passport_date;

        $user->user_type = $request->user_type;
        $user->lead_id = $request->lead_id;
        $user->employee_id = $request->employee_id;
        $user->access_block = $request->access_block;

        $user->author_id = $user_auth_id;

        // Если нет прав на создание полноценной записи - запись отправляем на модерацию
        if($answer['automoderate'] == false){
          $user->moderation = 1;
        }

        // Пишем ID компании авторизованного пользователя
        if($company_id == null){abort(403, 'Необходимо авторизоваться под компанией');};
        $user->company_id = $company_id;

        // Пишем ID филиала авторизованного пользователя
        if($filial_id == null){abort(403, 'Операция невозможна. Вы не являетесь сотрудником!');};
        $user->filial_id = $filial_id;


        // Создаем папку в файловой системе
        // $link_for_folder = 'public/companies/' . $company_id . '/'. $filial_id . '/users/' . $user->id . 'avatars';
        // Storage::makeDirectory($link_for_folder);

        // $link_for_folder = 'public/companies/' . $company_id . '/'. $filial_id . '/users/' . $user->id . 'photos';
        // Storage::makeDirectory($link_for_folder);

        // $link_for_folder = 'public/companies/' . $company_id . '/'. $filial_id . '/users/' . $user->id . 'video';
        // Storage::makeDirectory($link_for_folder);

        // $link_for_folder = 'public/companies/' . $company_id . '/'. $filial_id . '/users/' . $user->id . 'documents';
        // Storage::makeDirectory($link_for_folder);

        $company_id = $user_auth->company_id;
        if ($user_auth->god == 1) {
      // Если бог, то ставим автором робота
          $user_id = 1;
          $company_id = null;
        } else {
          $user_id = $user_auth->id;
        }

        if ($request->hasFile('photo')) {
          $photo = new Photo;
          $image = $request->file('photo');
          $directory = $user_auth->company->id.'/media/albums/'.$user->login.'/img/';
          $extension = $image->getClientOriginalExtension();
          $photo->extension = $extension;
          $image_name = 'avatar.'.$extension;

          // $photo->path = '/'.$directory.'/'.$image_name;

          $params = getimagesize($request->file('photo'));
          $photo->width = $params[0];
          $photo->height = $params[1];

          $size = filesize($request->file('photo'))/1024;
          $photo->size = number_format($size, 2, '.', '');

          $photo->name = $image_name;
          $photo->company_id = $company_id;
          $photo->author_id = $user_id;
          $photo->save();

          $upload_success = $image->storeAs($directory, 'original-'.$image_name, 'public');

          $avater = Image::make($request->photo)->widen(30);
          $save_path = storage_path('app/public/'.$directory);
          if (!file_exists($save_path)) {
            mkdir($save_path, 666, true);
          }
          $avater->save(storage_path('app/public/'.$directory.$image_name));

          

          $user->photo = $photo->path;
          $user->photo_id = $photo->id;
        }   

        $user->save();
        if ($user) {
          // Когда новость обновилась, смотрим пришедние для нее альбомы и сравниваем с существующими
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

          // dd($mass);
            DB::table('role_user')->insert($mass);

          } else {
            // Если удалили последнюю роль для должности и пришел пустой массив
            $delete = RoleUser::whereUser_id($user->id)->delete();
          }
          return redirect('users');
        } else {
          abort(403, 'Ошибка при обновлении пользователя!');
        }
      }

      public function show(Request $request, $id)
      {

        // ГЛАВНЫЙ ЗАПРОС:
        $user = User::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $user);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // Функция из Helper отдает массив со списками для SELECT
        $departments_list = getLS('users', 'view', 'departments');
        $filials_list = getLS('users', 'view', 'filials');

        $role = new Role;
        $role_users = RoleUser::with('role', 'department', 'position')->whereUser_id($user->id)->get();

        $answer_roles = operator_right('roles', false, 'index');

        $roles_list = Role::moderatorLimit($answer_roles)
        ->companiesLimit($answer_roles)
        ->filials($answer_roles) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
        ->authors($answer_roles)
        ->systemItem($answer_roles) // Фильтр по системным записям 
        ->pluck('name', 'id');

        return view('users.edit', compact('user', 'role', 'role_users', 'roles_list', 'departments_list', 'filials_list'));
      }

      public function edit(Request $request, $id)
      {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $user = User::with('location.city', 'roles', 'role_user', 'role_user.role', 'role_user.position', 'role_user.department', 'avatar')->moderatorLimit($answer)->findOrFail($id);

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

        // dd($departments_list);
        // Инфо о странице
        $page_info = pageInfo($this->entity_name);
        // dd($user);
        
        return view('users.edit', compact('user', 'role', 'role_users', 'roles_list', 'departments_list', 'filials_list', 'page_info'));
      }

      public function update(UserRequest $request, $id)
      {
        // Получаем авторизованного пользователя
        $user_auth = $request->user();
        $user_auth_id = hideGod($user_auth);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $user = User::with('location', 'company', 'photo')->moderatorLimit($answer)->findOrFail($id);


        $filial_id = $request->filial_id;

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $user);


        // Пишем локацию
        $location = $user->location;
        if($location->city_id != $request->city_id) {
          $location->city_id = $request->city_id;
          $location->editor_id = $user_auth_id;
          $location->save();
        }
        if($location->address = $request->address) {
          $location->address = $request->address;
          $location->editor_id = $user_auth_id;
          $location->save();
        }

        $user->login = $request->login;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->nickname = $request->nickname;

        $user->first_name = $request->first_name;
        $user->second_name = $request->second_name;
        $user->patronymic = $request->patronymic;
        $user->sex = $request->sex;
        $user->birthday = $request->birthday;

        $user->phone = cleanPhone($request->phone);

        if(($request->extra_phone != NULL)&&($request->extra_phone != "")){
          $user->extra_phone = cleanPhone($request->extra_phone);
        } else {$user->extra_phone = NULL;};

        $user->telegram_id = $request->telegram_id;

        $user->orgform_status = $request->orgform_status;

        $user->user_inn = $request->inn;

        $user->passport_address = $request->passport_address;
        $user->passport_number = $request->passport_number;
        $user->passport_released = $request->passport_released;
        $user->passport_date = $request->passport_date;

        $user->user_type = $request->user_type;
        
        $user->lead_id = $request->lead_id;
        $user->employee_id = $request->employee_id;
        $user->access_block = $request->access_block;

        $user->filial_id = $request->filial_id;



        $company_id = $user_auth->company_id;
        if ($user_auth->god == 1) {
      // Если бог, то ставим автором робота
          $user_id = 1;
          $company_id = null;
        } else {
          $user_id = $user_auth->id;
        }

        if ($request->hasFile('photo')) {
          $photo = new Photo;
          $image = $request->file('photo');
          $directory = $user_auth->company->id.'/media/albums/'.$user->login.'/img/';
          $extension = $image->getClientOriginalExtension();
          $photo->extension = $extension;
          $image_name = 'avatar.'.$extension;

          // $photo->path = '/'.$directory.'/'.$image_name;

          $params = getimagesize($request->file('photo'));
          $photo->width = $params[0];
          $photo->height = $params[1];

          $size = filesize($request->file('photo'))/1024;
          $photo->size = number_format($size, 2, '.', '');

          $photo->name = $image_name;
          $photo->company_id = $company_id;
          $photo->author_id = $user_id;
          $photo->save();

          $upload_success = $image->storeAs($directory, 'original-'.$image_name, 'public');

          $avater = Image::make($request->photo)->widen(30);
          $save_path = storage_path('app/public/'.$directory);
          if (!file_exists($save_path)) {
            mkdir($save_path, 666, true);
          }
          $avater->save(storage_path('app/public/'.$directory.$image_name));

          // $user->photo = $photo->path;
          $user->photo_id = $photo->id;
        }   

        // Модерируем (Временно)
        if($answer['automoderate']){$user->moderation = null;};

        $user->save();

        // dd($request->access);

        if ($user) {
          // Когда новость обновилась, смотрим пришедние для нее альбомы и сравниваем с существующими
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

          // dd($mass);
            DB::table('role_user')->insert($mass);

          } else {
        // Если удалили последнюю роль для должности и пришел пустой массив
            $delete = RoleUser::whereUser_id($user->id)->delete();
          }
          return redirect('users');
        } else {
          abort(403, 'Ошибка при обновлении пользователя!');
        }

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
        $user = User::moderatorLimit($answer)->where('id', $id)->delete();

        if($user) {return Redirect('/users');} else {abort(403,'Что-то пошло не так!');};
      }


    // --------------------------------------------------------------------------------------------------------------------------------------------------------------
    // СПЕЦИФИЧЕСКИЕ МЕТОДЫ СУЩНОСТИ
    // --------------------------------------------------------------------------------------------------------------------------------------------------------------

      public function getauthcompany($company_id)
      {

        // Только для бога
        $this->authorize('god', User::class);

        $auth_user = User::findOrFail(Auth::user()->id);
        $auth_user->company_id = $company_id;
        $auth_user->save();

        return redirect('/getaccess');
      }

      public function getauthuser($user_id)
      {

        // Только для бога
        $this->authorize('god', User::class);
        session(['god' => Auth::user()->id]);
        Auth::loginUsingId($user_id);
        return redirect('/getaccess');
      }

      public function getgod()
      {
            // Только для бога
        $this->authorize('god', User::class);

        $user = User::findOrFail(Auth::user()->id);
        $user->company_id = null;
        $user->save();

        return redirect('/getaccess');
      }

      public function returngod(Request $request)
      {

        if ($request->session()->has('god')) {

          $god_id = $request->session()->get('god');
          $request->session()->forget('god');
          Auth::loginUsingId($god_id);
        }

        return redirect('/getaccess');
      }

    }
