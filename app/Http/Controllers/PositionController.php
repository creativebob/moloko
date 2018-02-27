<?php

namespace App\Http\Controllers;

// Подключаем модели
use App\Position;
use App\Page;
use App\User;
use App\Role;
use App\Staffer;
use App\PositionRole;
// Валидация
use App\Http\Requests\PositionRequest;
// Политика
use App\Policies\PostionPolicy;
// Подключаем фасады
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PositionController extends Controller
{
  // Сущность над которой производит операции контроллер
  protected $entity_name = 'positions';
  protected $entity_dependence = false;

  public function index(Request $request)
  {

    // Подключение политики
    $this->authorize(getmethod(__FUNCTION__), Position::class);

    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

    // -------------------------------------------------------------------------------------------
    // ГЛАВНЫЙ ЗАПРОС
    // -------------------------------------------------------------------------------------------

    $positions = Position::with('author', 'page')
    ->moderatorLimit($answer)
    ->companiesLimit($answer)
    ->filials($answer) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
    ->authors($answer)
    ->systemItem($answer) // Фильтр по системным записям
    ->template($answer) // Выводим шаблоны в список
    ->orderBy('moderation', 'desc')
    ->paginate(30);

    // Инфо о странице
    $page_info = pageInfo($this->entity_name);

    return view('positions.index', compact('positions', 'page_info'));
  }

  public function create(Request $request)
  {

    // Подключение политики
    $this->authorize(getmethod(__FUNCTION__), Position::class);

    // Список посадочных страниц для должности
    $answer_pages = operator_right('pages', false, 'index');
    $pages_list = Page::moderatorLimit($answer_pages)
    ->whereSite_id(1) // Только для должностей посадочная страница системного сайта
    // ->companiesLimit($answer_pages)
    ->authors($answer_pages)
    ->systemItem($answer_pages) // Фильтр по системным записям
    ->template($answer_pages)
    ->pluck('page_name', 'id');

    // Список ролей для должности
    $answer_roles = operator_right('roles', false, 'index');
    $roles = Role::moderatorLimit($answer_roles)
    ->companiesLimit($answer_roles)
    ->filials($answer_roles) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
    ->authors($answer_roles)
    ->systemItem($answer_roles) // Фильтр по системным записям
    ->template($answer_pages)
    ->get();

    $position = new Position;

    // Инфо о странице
    $page_info = pageInfo($this->entity_name);
    return view('positions.create', compact('position', 'pages_list', 'roles', 'page_info'));  
  }

  public function store(PositionRequest $request)
  {

    // Подключение политики
    $this->authorize(getmethod(__FUNCTION__), Position::class);

    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

    // Получаем данные для авторизованного пользователя
    $user = $request->user();

    if ($user->god == 1) {
      $user_id = 1;
    } else {
      $user_id = $user->id;
    };

    $user_status = $user->god;
    $company_id = $user->company_id;

    // Создаем новую должность
    $position = new Position;
    $position->position_name = $request->position_name;
    $position->page_id = $request->page_id;
    $position->author_id = $user_id;

    // Если нет прав на создание полноценной записи - запись отправляем на модерацию
    if($answer['automoderate'] == false){
        $position->moderation = 1;
    };

    // Пишем ID компании авторизованного пользователя
    if($company_id == null) {
      abort(403, 'Необходимо авторизоваться под компанией');
    };

    $position->company_id = $company_id;

    $position->save();

    // Если должность записалась
    if($position) {

      $mass = [];

      if($request->roles){

        // Смотрим список пришедших роллей
        foreach ($request->roles as $role) {

          $mass[] = [
            'position_id' => $position->id,
            'role_id' => $role,
            'author_id' => $user_id,
          ];
        }

        DB::table('position_role')->insert($mass);
      }



      return Redirect('/positions');

    } else {
      abort(403, 'Ошибка записи должности');
    };
  }

  public function show($id)
  {
      //
  }

  public function edit(Request $request, $id)
  {

    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

    // ГЛАВНЫЙ ЗАПРОС:
    $position = Position::moderatorLimit($answer)->findOrFail($id);

    // Подключение политики
    $this->authorize(getmethod(__FUNCTION__), $position);

    // Список посадочных страниц для должности
    $answer_pages = operator_right('pages', false, 'index');
    $pages_list = Page::moderatorLimit($answer_pages)
    // ->companiesLimit($answer_pages)
    ->whereSite_id(1) // Только для должностей посадочная страница системного сайта
    ->authors($answer_pages)
    ->systemItem($answer_pages) // Фильтр по системным записям
    ->template($answer_pages)
    ->pluck('page_name', 'id');

    // Список ролей для должности
    $answer_roles = operator_right('roles', false, 'index');
    $roles = Role::moderatorLimit($answer_roles)
    ->companiesLimit($answer_roles)
    ->filials($answer_roles) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
    ->authors($answer_roles)
    ->systemItem($answer_roles) // Фильтр по системным записям
    ->template($answer_pages)
    ->get();

    // Инфо о странице
    $page_info = pageInfo($this->entity_name);

    return view('positions.edit', compact('position', 'pages_list', 'roles', 'page_info'));
  }

  public function update(PositionRequest $request, $id)
  {

    // Получаем авторизованного пользователя
    $user = $request->user();

    if ($user->god == 1) {
      $user_id = 1;
    } else {
      $user_id = $user->id;
    };

    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

    // ГЛАВНЫЙ ЗАПРОС:
    $position = Position::moderatorLimit($answer)->findOrFail($id);

    // Подключение политики
    $this->authorize(getmethod(__FUNCTION__), $position);

    // Выбираем существующие роли для должности на данный момент
    $position_roles = $position->roles;

    // Перезаписываем данные
    $position->position_name = $request->position_name;
    $position->page_id = $request->page_id;

    // $position->company_id = $user->company_id;
    $position->editor_id = $user_id;
    $position->save();
    // Если записалось
    if ($position) {
      // Когда должность обновилась, смотрим пришедние для нее роли и сравниваем с существующими
      if (isset($request->roles)) {
        $delete = PositionRole::wherePosition_id($id)->delete();
        $mass = [];
        // Смотрим список пришедших роллей
        foreach ($request->roles as $role) {
          $mass[] = [
            'position_id' => $id,
            'role_id' => $role,
            'author_id' => $user->id,
          ];
        };
        DB::table('position_role')->insert($mass);
        // Лехина идея с перебором на соответствие
        // $p = 0;
        // foreach ($position_roles as $position_role) {
        //   foreach ($request->roles as $role) {
        //     if ($position_role->id == $role) {
        //       $p = 1;
        //     };
        //   };
        //   if ($p == 0) {
        //     $delete = PositionRole::wherePosition_id($id)->whereRole_id($position_role->id)->get();
        //     PositionRole::destroy();
        //   }
        // }
      } else {
        // Если удалили последнюю роль для должности и пришел пустой массив
        $delete = PositionRole::wherePosition_id($id)->delete();
      }
      return Redirect('/positions');
    } else {
      abort(403, 'Ошибка записи должности');
    };
  }

  public function destroy(Request $request, $id)
  {

    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, true, getmethod(__FUNCTION__));

    // ГЛАВНЫЙ ЗАПРОС:
    $position = Position::moderatorLimit($answer)->findOrFail($id);

    // Подключение политики
    $this->authorize(getmethod(__FUNCTION__), $position);

    // Поулчаем авторизованного пользователя
    $user = $request->user();

    if (isset($position)) {
      $position->editor_id = $user->id;
      $position->save();
      // Удаляем должность с обновлением
      $position = Position::destroy($id);
      if ($position) {
        return Redirect('/positions');
      } else {
        abort(403, 'Ошибка при удалении должности');
      }; 
    } else {
      abort(403, 'Должность не найдена');
    };
  }

  public function positions_list(Request $request)
  {

    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer_staff = operator_right('staff', 'true', 'index');

    // Смотрим на наличие должности в данном филиале, в массиве устанавливаем id должностей, которых не може тбыть более 1ой
    $direction = Staffer::where(['position_id' => 1, 'filial_id' => $request->filial_id])->moderatorLimit($answer_staff)->count();

    $repeat = [];

    if($direction == 1) {
      $repeat[] = 1;
    };

    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

    // -------------------------------------------------------------------------------------------
    // ГЛАВНЫЙ ЗАПРОС
    // -------------------------------------------------------------------------------------------

    $positions_list = Position::with('staff')->moderatorLimit($answer)
    ->companiesLimit($answer)
    ->filials($answer) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
    ->authors($answer)
    ->systemItem($answer) // Фильтр по системным записям
    ->template($answer) // Выводим шаблоны в список
    ->whereNotIn('id', $repeat)
    ->pluck('position_name', 'id');
    echo json_encode($positions_list, JSON_UNESCAPED_UNICODE);
  }
}
