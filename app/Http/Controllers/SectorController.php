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
    // Если индустрия
    if ($request->first_item == 1) {

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
    }

    // Если сектор
    if ($request->medium_item == 1) {

      $sector->sector_name = $request->name;
      $sector->sector_parent_id = $request->parent;
      
      $sector->save();

      // Проверяем права
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
          'parent' => $sector->sector_parent_id,
          'create' => $create,
          'edit' => $edit,
          'delete' => $delete
        ];
      } else {
        $result = [
          'error_status' => 1,
          'error_message' => 'Ошибка при записи сектора!'
        ];
      }
    }

    // Отдаем результат
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
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
        'name' => $sector->sector_name,
        'parent_id' => $sector->sector_parent_id,
        'first_id' => $sector->industry_id,
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

    // Если индустрия
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
    };

    // Если сектор
    if ($request->medium_item == 1) {
      $sector->sector_name = $request->name;
      $sector->sector_parent_id = $request->parent;
      $sector->editor_id = $user->id;
      $sector->save();

      // Проверяем права
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
          'parent' => $sector->sector_parent_id,
          'create' => $create,
          'edit' => $edit,
          'delete' => $delete,
        ];
      } else {
        $result = [
          'error_status' => 1,
          'error_message' => 'Ошибка при изменении сектора!',
        ];
      }
    };

    // Отдаем результат
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
  }

  public function destroy(Request $request, $id)
  {
    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

    // ГЛАВНЫЙ ЗАПРОС:
    $sector = Sector::moderatorLimit($answer)->findOrFail($id);

    $parent = null;
    if (isset($sector->sector_parent_id)) {
      $parent = $sector->sector_parent_id;
    }

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
          'parent' => $parent,
          'msg' => 'Успешно удалено'
        ];
      } else {
        // В случае непредвиденной ошибки
        $data = [
          'status' => 1,
          'msg' => 'Произошла непредвиденная ошибка, попробуйте перезагрузить страницу и попробуйте еще раз'
        ];
      };
    };
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
  }

  public function sector_check(Request $request)
  {
    // Проверка отдела в нашей базе данных
    $sector = Sector::where('sector_name', $request->name)->first();

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

  public function sectors_list(Request $request)
  {
    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_dependence, 'index');

    // Главный запрос
    $sectors = Sector::moderatorLimit($answer)
    // ->whereNotIn(['id' => [$request->id], 'sector_parent_id' => [$request->id]])
    ->get(['id','sector_name','industry_status','sector_parent_id'])
    // ->pluck('id','sector_name','industry_status')
    ->keyBy('id')
    ->toArray();

    // Получаем список секторов
    $sectors_cat = [];
    foreach ($sectors as $id => &$node) {  
     
        // Если нет вложений
        if (!$node['sector_parent_id']) {
          $sectors_cat[$id] = &$node;
        } else { 
        // Если есть потомки то перебераем массив
          $sectors_cat[$node['sector_parent_id']]['children'][$id] = &$node;
        };
      
    };

    // // dd($sectors_cat);
    $sectors_list = [];
    foreach ($sectors_cat as $id => &$node) {
      if ($id != $request->id) { 
        $sectors_list[$id] = &$node;
        if (isset($node['children'])) {
          foreach ($node['children'] as $id => &$node) {
            $sectors_list[$id] = &$node;
          }
        };
      };
    };

    echo json_encode($sectors_cat, JSON_UNESCAPED_UNICODE);
    // dd($sectors_list);
  }
}
