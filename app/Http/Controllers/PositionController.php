<?php

namespace App\Http\Controllers;

// Подключаем модели
use App\Position;
use App\Page;
use App\User;
use App\Role;
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
    // Получаем метод
    $method = __FUNCTION__;
    // Подключение политики
    $this->authorize($method, Position::class);
    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_dependence, $method);
    // dd($answer);
    // -------------------------------------------------------------------------------------------
    // ГЛАВНЫЙ ЗАПРОС
    // -------------------------------------------------------------------------------------------
    $positions = Position::with('author', 'page')
    ->withoutGlobalScope($answer['moderator'])
    ->moderatorFilter($answer)
    ->companiesFilter($answer)
    ->filials($answer) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
    ->authors($answer)
    ->systemItem($answer) // Фильтр по системным записям
    ->template($answer) // Выводим шаблоны в список
    ->orderBy('moderated', 'desc')
    ->paginate(30);

    // Инфо о странице
    $page_info = pageInfo($this->entity_name);
    return view('positions.index', compact('positions', 'page_info'));
  }

  public function create(Request $request)
  {
    // Получаем метод
    $method = __FUNCTION__;
    // Подключение политики
    $this->authorize($method, Position::class);

    // Список посадочных страниц для должности
    $answer = operator_right('pages', $this->entity_dependence, $method);
    $pages_list = Page::withoutGlobalScope($answer['moderator'])
    ->moderatorFilter($answer)
    ->companiesFilter($answer)
    ->filials($answer) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
    ->authors($answer)
    ->systemItem($answer) // Фильтр по системным записям
    ->whereSite_id(1) // Только для должностей посадочная страница системного сайта
    ->pluck('page_name', 'id');

    // Список ролей для должности
    $answer = operator_right('pages', $this->entity_dependence, $method);
    $roles = Role::withoutGlobalScope($answer['moderator'])
    ->moderatorFilter($answer)
    ->companiesFilter($answer)
    ->filials($answer) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
    ->authors($answer)
    ->systemItem($answer) // Фильтр по системным записям
    ->get();

    $position = new Position;
    return view('positions.create', compact('position', 'pages_list', 'roles'));  
  }

  public function store(PositionRequest $request)
  {
    // Получаем метод
    $method = 'create';
    // Подключение политики
    $this->authorize($method, Position::class);
    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_dependence, $method);

    // Получаем данные для авторизованного пользователя
    $user = $request->user();
    $user_id = $user->id;
    $user_status = $user->god;
    $company_id = $user->company_id;

    // Создаем новую должность
    $position = new Position;
    $position->position_name = $request->position_name;
    $position->page_id = $request->page_id;
    $position->author_id = $user_id;
    // Если нет прав на создание полноценной записи - запись отправляем на модерацию
    if($answer['automoderate'] == false){
        $position->moderated = 1;
    };
    // Пишем ID компании авторизованного пользователя
    if($company_id == null) {
      abort(403, 'Необходимо авторизоваться под компанией');
    };
    $position->company_id = $company_id;
    $position->save();
    // Если должность записалась
    if ($position) {
      $mass = [];
      // Смотрим список пришедших роллей
      foreach ($request->roles as $role) {
        $mass[] = [
          'position_id' => $position->id,
          'role_id' => $role,
          'author_id' => $user_id,
        ];
      }
      DB::table('position_role')->insert($mass);
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
    // Получаем метод
    $method = 'update';
    // ГЛАВНЫЙ ЗАПРОС:
    $position = Position::withoutGlobalScope(ModerationScope::class)->findOrFail($id);
    // Подключение политики
    $this->authorize($method, $position);

    // Список посадочных страниц для должности
    $answer = operator_right('pages', $this->entity_dependence, $method);
    $pages_list = Page::withoutGlobalScope($answer['moderator'])
    ->moderatorFilter($answer)
    ->companiesFilter($answer)
    ->filials($answer) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
    ->authors($answer)
    ->systemItem($answer) // Фильтр по системным записям
    ->whereSite_id(1) // Только для должностей посадочная страница системного сайта
    ->pluck('page_name', 'id');

    // Список ролей для должности
    $answer = operator_right('pages', $this->entity_dependence, 'update');
    $roles = Role::withoutGlobalScope($answer['moderator'])
    ->moderatorFilter($answer)
    ->companiesFilter($answer)
    ->filials($answer) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
    ->authors($answer)
    ->systemItem($answer) // Фильтр по системным записям
    ->get();

    return view('positions.edit', compact('position', 'pages_list', 'roles'));
  }

  public function update(PositionRequest $request, $id)
  {
    // Получаем метод
    $method = __FUNCTION__;
    // Получаем авторизованного пользователя
    $user = $request->user();
    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, true, $method);
    // ГЛАВНЫЙ ЗАПРОС:
    $position = Position::withoutGlobalScope($answer['moderator'])->findOrFail($id);
    // Подключение политики
    $this->authorize('update', $position);
    // Выбираем существующие роли для должности на данный момент
    $position_roles = $position->roles;
    // Перезаписываем данные
    $position->position_name = $request->position_name;
    $position->page_id = $request->page_id;
    $position->company_id = $user->company_id;
    $position->editor_id = $user->id;
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
    // ГЛАВНЫЙ ЗАПРОС:
    $position = Position::withoutGlobalScope($answer['moderator'])->findOrFail($id);
    // Подключение политики
    $this->authorize('delete', $position);
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
}
