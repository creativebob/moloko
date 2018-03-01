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

    // Получаем список секторов
    $sectors_db = Sector::get()->keyBy('id')->toArray();
    $sectors_cat = [];
    foreach ($sectors_db as $id => &$node) {   
      //Если нет вложений
      if (!$node['sector_parent_id']){
        $sectors_cat[$id] = &$node;
      } else { 
      //Если есть потомки то перебераем массив
        $sectors_db[$node['sector_parent_id']]['children'][$id] = &$node;
      };
    };
    // dd($sectors_cat);
    $sectors_list = [];
    foreach ($sectors_cat as $id => &$node) {
      $sectors_list[$id] = &$node;
      if (isset($node['children'])) {
        foreach ($node['children'] as $id => &$node) {
          $sectors_list[$id] = &$node;
        }
      };
    };

    // Инфо о странице
    $page_info = pageInfo($this->entity_name);

    // dd($sectors_tree);
    return view('sectors.index', compact('sectors_tree', 'page_info', 'sectors', 'sectors_list'));
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

    // Получаем список секторов
    $sectors_db = Sector::get()->keyBy('id')->toArray();
    $sectors_cat = [];
    foreach ($sectors_db as $id => &$node) {   
      //Если нет вложений
      if (!$node['sector_parent_id']){
        $sectors_cat[$id] = &$node;
      } else { 
      //Если есть потомки то перебераем массив
        $sectors_db[$node['sector_parent_id']]['children'][$id] = &$node;
      };
    };
    // dd($sectors_cat);
    $sectors_list = [];
    foreach ($sectors_cat as $id => &$node) {
      $sectors_list[$id] = &$node;
      if (isset($node['children'])) {
        foreach ($node['children'] as $id => &$node) {
          $sectors_list[$id] = &$node;
        }
      };
    };

    // Инфо о странице
    $page_info = pageInfo($this->entity_name);
    
    $data = [
      'section_name' => 'sectors',
      'item_name' => 'staff',
      'section_id' => $section_id,
      'item_id' => $item_id,
    ];
    return view('sectors.index', compact('sectors_tree', 'data', 'page_info', 'sectors', 'sectors_list')); 
  }

  public function create()
  {
    //
  }

  public function store(Request $request)
  {
    // Подключение политики
    $this->authorize(getmethod(__FUNCTION__), Sector::class);

    // Получаем данные для авторизованного пользователя
    $user = $request->user();
    $company_id = $user->company_id;
    if ($user->god == 1) {
      $user_id = 1;
    } else {
      $user_id = $user->id;
    }

    // Пишем в базу
    $sector = new Sector;
    $sector->company_id = $company_id;
    $sector->author_id = $user_id;

    // Смотрим что пришло
    if ($request->first_item == 1) {

      // Если индустрия
      $sector->sector_name = $request->name;
      $sector->industry_status = 1;
      
      $sector->save();

      $create = 0;
      $edit = 0;
      $delete = 0;
      if ($user->can('create', Sector::class)) {
        $create = 1;
      };
      if ($user->can('update', $sector)) {
        $edit = 1;
      };
      if ($user->can('delete', $sector)) {
        $delete = 1;
      };

      if ($sector) {
        $result = [
          'error_status' => 0,
          'id' => $sector->id,
          'name' => $sector->sector_name,
          'create' => $create,
          'edit' => $edit,
          'delete' => $delete
        ];
      } else {
        $result = [
          'error_status' => 1,
          'error_message' => 'Ошибка при записи индустрии!'
        ];
      }
      echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }
    

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
        'id' => $sector->id,
        'name' => $sector->sector_name,
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
    // Получаем авторизованного пользователя
    $user = $request->user();

    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_name, getmethod(__FUNCTION__))
    ;
    // ГЛАВНЫЙ ЗАПРОС:
    $sector = Sector::moderatorLimit($answer)->findOrFail($id);

    // Подключение политики
    $this->authorize(getmethod(__FUNCTION__), $sector);

    if ($request->first_item == 1) {
      $sector->sector_name = $request->name;
      $sector->editor_id = $user->id;
      $sector->save();

      if ($sector) {
        $result = [
          'error_status' => 0,
          'id' => $sector->id,
          'name' => $sector->sector_name,
        ];
      } else {
        $result = [
          'error_status' => 1,
          'error_message' => 'Ошибка при изменении индустрии!'
        ];
      }
      echo json_encode($result, JSON_UNESCAPED_UNICODE);
    };

  }

  public function destroy(Request $request, $id)
  {
    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

    // ГЛАВНЫЙ ЗАПРОС:
    $sector = Sector::moderatorLimit($answer)->findOrFail($id);

    // Подключение политики
    $this->authorize(getmethod(__FUNCTION__), $sector);

    // Удаляем ajax
    // Проверяем содержит ли индустрия вложения
    $sector_parent = Sector::moderatorLimit($answer)->whereSector_parent_id($id)->first();

    // Получаем авторизованного пользователя
    $user = $request->user();

    // Если содержит, то даем сообщенеи об ошибке
    if ($sector_parent) {
      // Если содержит, то даем сообщенеи об ошибке
      $data = [
        'status' => 1,
        'msg' => 'Данная область содержит населенные пункты, удаление невозможно'
      ];
      // Если нет, мягко удаляем
    } else {

      $sector->editor_id = $user->id;
      $sector->save();

      // Если нет, мягко удаляем
      $sector = Sector::destroy($id);

      if ($sector){
        $data = [
          'status'=> 0,
          'type' => 'sectors',
          'id' => $id,
          'msg' => 'Успешно удалено'
        ];
      } else {
        // В случае непредвиденной ошибки
        $data = [
          'status' => 0,
          'msg' => 'Произошла непредвиденная ошибка, попробуйте перезагрузить страницу и попробуйте еще раз'
        ];
      };
    };
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
  }

  public function sector_check(Request $request)
  {
    // Проверка отдела в нашей базе данных
    $sector = Sector::where('sector_name',$request->sector_name)->first();

    // Если такое название есть
    if ($sector) {
      $result = [
        'error_status' => 1,
      ];
    // Если нет
    } else {
      $result = [
        'error_status' => 0
      ];
    };
    return json_encode($result, JSON_UNESCAPED_UNICODE);
  }
}
