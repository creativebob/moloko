<?php

namespace App\Http\Controllers;

// Подключаем модели
use App\Sector;

// Валидация
use App\Http\Requests\SectorRequest;
// Политика
use App\Policies\SectorPolicy;
// Подключаем фасады
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SectorController extends Controller
{
  // Сущность над которой производит операции контроллер
  protected $entity_name = 'sectors';
  protected $entity_dependence = false;

  public function index(Request $request)
  {

    // Подключение политики
    $this->authorize(getmethod(__FUNCTION__), Sector::class);

    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

    // -----------------------------------------------------------------------------------------------------------------------
    // ГЛАВНЫЙ ЗАПРОС
    // -----------------------------------------------------------------------------------------------------------------------
    $sectors = Sector::moderatorLimit($answer)
    ->companiesLimit($answer)
    ->filials($answer) // $industrys должна существовать только для зависимых от филиала, иначе $industrys должна null
    ->authors($answer)
    ->systemItem($answer) // Фильтр по системным записям
    ->orderBy('moderation', 'desc')
    ->get();

    // Создаем масив где ключ массива является ID меню
    $sectors_rights = [];
    $sectors_rights = $sectors->keyBy('id');

    // Получаем данные для авторизованного пользователя
    $user = $request->user();

    // Проверяем прапва на редактирование и удаление
    $sectors_id = [];
    foreach ($sectors_rights as $sector) {
      $edit = 0;
      $delete = 0;
      if ($user->can('update', $sector)) {
        $edit = 1;
      };
      if ($user->can('delete', $sector)) {
        $delete = 1;
      };
      $sector_right = $sector->toArray();
      $sectors_id[$sector_right['id']] = $sector_right;
      $sectors_id[$sector_right['id']]['edit'] = $edit;
      $sectors_id[$sector_right['id']]['delete'] = $delete;
    };

    // dd($sectors_id);
    // Функция построения дерева из массива от Tommy Lacroix
    $sectors_tree = [];
    foreach ($sectors_id as $id => &$node) {   
      //Если нет вложений
      if (!$node['sector_parent_id']){
        $sectors_tree[$id] = &$node;
      } else { 
      //Если есть потомки то перебераем массив
        $sectors_id[$node['sector_parent_id']]['children'][$id] = &$node;
      }
    };

    foreach ($sectors_tree as $sector) {
      $count = 0;
      if (isset($sector['children'])) {
        $count = count($sector['children']) + $count;
      };
      $sectors_tree[$sector['id']]['count'] = $count;
      // dd($sector);
    };

    // Инфо о странице
    $page_info = pageInfo($this->entity_name);

    // dd($sectors_tree);
    return view('sectors.index', compact('sectors_tree', 'page_info', 'sectors'));
  }

  // Получаем сторонние данные по 
  public function current_sector(Request $request, $section_id, $item_id)
  {
    // Подключение политики
    $this->authorize(getmethod(__FUNCTION__), Sector::class);

    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

    // -----------------------------------------------------------------------------------------------------------------------
    // ГЛАВНЫЙ ЗАПРОС
    // -----------------------------------------------------------------------------------------------------------------------
    $sectors = Sector::moderatorLimit($answer)
    ->companiesLimit($answer)
    ->filials($answer) // $industrys должна существовать только для зависимых от филиала, иначе $industrys должна null
    ->authors($answer)
    ->systemItem($answer) // Фильтр по системным записям
    ->orderBy('moderation', 'desc')
    ->get();

    // Создаем масив где ключ массива является ID меню
    $sectors_rights = [];
    $sectors_rights = $sectors->keyBy('id');

    // Получаем данные для авторизованного пользователя
    $user = $request->user();

    // Проверяем прапва на редактирование и удаление
    $sectors_id = [];
    foreach ($sectors_rights as $sector) {
      $edit = 0;
      $delete = 0;
      if ($user->can('update', $sector)) {
        $edit = 1;
      };
      if ($user->can('delete', $sector)) {
        $delete = 1;
      };
      $sector_right = $sector->toArray();
      $sectors_id[$sector_right['id']] = $sector_right;
      $sectors_id[$sector_right['id']]['edit'] = $edit;
      $sectors_id[$sector_right['id']]['delete'] = $delete;
    };

    // dd($sectors_id);
    // Функция построения дерева из массива от Tommy Lacroix
    $sectors_tree = [];
    foreach ($sectors_id as $id => &$node) {   
      //Если нет вложений
      if (!$node['sector_parent_id']){
        $sectors_tree[$id] = &$node;
      } else { 
      //Если есть потомки то перебераем массив
        $sectors_id[$node['sector_parent_id']]['children'][$id] = &$node;
      }
    };

    foreach ($sectors_tree as $sector) {
      $count = 0;
      if (isset($sector['children'])) {
        $count = count($sector['children']) + $count;
      };
      $sectors_tree[$sector['id']]['count'] = $count;
      // dd($sector);
    };
    // Инфо о странице
    $page_info = pageInfo($this->entity_name);
    
    $data = [
      'section_name' => 'sectors',
      'item_name' => 'staff',
      'section_id' => $section_id,
      'item_id' => $item_id,
    ];
    return view('sectors.index', compact('sectors_tree', 'data', 'page_info', 'sectors')); 
  }

  public function create()
  {
    //
  }

  public function store(Request $request)
  {
    //
  }

  public function show($id)
  {
    //
  }

  public function edit($id)
  {
    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, true, getmethod(__FUNCTION__));

    // ГЛАВНЫЙ ЗАПРОС:
    $sector = Sector::moderatorLimit($answer)->findOrFail($id);

    // Подключение политики
    $this->authorize(getmethod(__FUNCTION__), $sector);

    if ($sector->industry_status == 1) {
      // Меняем филиал
      $result = [
        'industry_name' => $sector->sector_name,
      ];
    } else {
      $result = [
        'sector_name' => $sector->sector_name,
        'sector_parent_id' => $sector->sector_parent_id,
        'industry_id' => $sector->industry_id,
      ];
    };
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
  }

  public function update(Request $request, $id)
  {
    //
  }

  public function destroy(Request $request, $id)
  {
    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

    // ГЛАВНЫЙ ЗАПРОС:
    $sector = Sector::moderatorLimit($answer)->findOrFail($id);

    // Подключение политики
    $this->authorize(getmethod(__FUNCTION__), $sector);

    $user = $request->user();

    if (isset($sector->sector_parent_id)) {
      abort(403, 'Не пустой');
    } else {
      $industry_status = $sector->industry_status;
      if ($industry_status == null) {
        $industry_id = $sector->industry_id;
        $sector_parent_id = $sector->sector_parent_id;
        if ($sector_parent_id == $industry_id) {
          $sector_parent_id = 0;
        };
      } else {
        $sector_parent_id = 0;
      };
      $sector->editor_id = $user->id;
      $sector->save();
      $sector = Sector::destroy($id);
      if ($sector){
        if ($industry_status == 1) {
          return Redirect('/sectors');
        } else {
          return Redirect('/current_sector/'.$industry_id.'/'.$sector_parent_id);
        };
      } else {
        abort(403, 'Ошибка при удалении филиала/отдела');
      };
    }
  }

  public function sector_check(Request $request)
  {

    // Проверка отдела в нашей базе данных
    $sector = Sector::where([
      ['sector_name', '=', $request->sector_name],
      ['industry_id', '=', $request->industry_id],
    ])->first();

    if ($sector) {
      $result = [
        'error_message' => 'Такой сектор уже существует',
        'error_status' => 0,
      ];
    } else {
      $result = [
        'error_status' => 1
      ];
    };
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
  }
}
