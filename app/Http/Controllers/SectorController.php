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
    ->filials($answer) // $industry должна существовать только для зависимых от филиала, иначе $industry должна null
    ->authors($answer)
    ->systemItem($answer) // Фильтр по системным записям
    ->orderBy('sort', 'asc')
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
      // Если нет вложений
      if (!$node['sector_parent_id']){
        $sectors_tree[$id] = &$node;
      } else { 
      // Если есть потомки то перебераем массив
        $sectors_id[$node['sector_parent_id']]['children'][$id] = &$node;
      }
    };

    foreach ($sectors_tree as $sector) {
      $count = 0;
      if (isset($sector['children'])) {
        $count = count($sector['children']) + $count;
      };
      $sectors_tree[$sector['id']]['count'] = $count;
    };

    // Инфо о странице
    $page_info = pageInfo($this->entity_name);

    // dd($sectors_tree);

    // Отдаем Ajax
    if($request->ajax()) {
      return view('sectors.industry-list', ['sectors_tree' => $sectors_tree, 'id' => $request->id]);
    }
    // Отдаем на шаблон
    return view('sectors.index', compact('sectors_tree', 'page_info'));
  }

  public function get_content(Request $request)
  {
    // Политика
    $this->authorize(getmethod('index'), Sector::class);

    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod('index'));

    // -----------------------------------------------------------------------------------------------------------------------
    // ГЛАВНЫЙ ЗАПРОС
    // -----------------------------------------------------------------------------------------------------------------------
    $sectors = Sector::moderatorLimit($answer)
    ->companiesLimit($answer)
    ->filials($answer) // $industry должна существовать только для зависимых от филиала, иначе $industry должна null
    ->authors($answer)
    ->systemItem($answer) // Фильтр по системным записям
    ->orderBy('sort', 'asc')
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
      // Если нет вложений
      if (!$node['sector_parent_id']){
        $sectors_tree[$id] = &$node;
      } else { 
      // Если есть потомки то перебераем массив
        $sectors_id[$node['sector_parent_id']]['children'][$id] = &$node;
      }
    };

    foreach ($sectors_tree as $sector) {
      $count = 0;
      if (isset($sector['children'])) {
        $count = count($sector['children']) + $count;
      };
      $sectors_tree[$sector['id']]['count'] = $count;
    };

    
    // Отдаем Ajax
    return view('sectors.industry-list', ['sectors_tree' => $sectors_tree, 'id' => $request->item_id]);
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
      // Если бог, то ставим автором робота
      $user_id = 1;
    } else {
      $user_id = $user->id;
    }

    // Пишем в базу
    $sector = new Sector;
    $sector->company_id = $company_id;
    $sector->author_id = $user_id;

    // Проверка на системную запись
    if (isset($request->system_item)) {
      $sector->system_item = $request->system_item;
    } else {
      $sector->system_item = null;
    }

    // Смотрим модерацию
    if (isset($request->moderation)) {
      $sector->moderation = $request->moderation;
    } else {
      $sector->moderation = null;
    }

    // Смотрим что пришло
    // Если индустрия
    if ($request->first_item == 1) {
      $sector->sector_name = $request->name;
      $sector->industry_status = 1;
      $sector->save();
    }

    // Если сектор
    if ($request->medium_item == 1) {
      $sector->sector_name = $request->name;
      $sector->sector_parent_id = $request->medium_parent_id;
      $sector->save();
    }

    if ($sector) {
      // Переадресовываем на index
      return redirect()->action('SectorController@get_content', ['item_id' => $sector->id]);
    } else {
      $result = [
        'error_status' => 1,
        'error_message' => 'Ошибка при записи сектора!'
      ];
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
      // Меняем индустрию
      $result = [
        'id' => $sector->id,
        'name' => $sector->sector_name,
        'moderation' => $sector->moderation,
        'system_item' => $sector->system_item,
      ];
    } else {
      $result = [
        'name' => $sector->sector_name,
        'parent_id' => $sector->sector_parent_id,
        'first_id' => $sector->industry_id,
        'moderation' => $sector->moderation,
        'system_item' => $sector->system_item,
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

    // Проверка на системную запись
    if ($request->system_item == 1) {
      $sector->system_item = $request->system_item;
    } else {
      $sector->system_item = null;
    }

    // Смотрим модерацию
    if ($request->moderation == 1) {
      $sector->moderation = $request->moderation;
    } else {
      $sector->moderation = null;
    }

    // Если индустрия
    if ($request->first_item == 1) {
      $sector->sector_name = $request->name;
      $sector->editor_id = $user->id;
      $sector->save();
    }

    // Если сектор
    if ($request->medium_item == 1) {
      $sector->sector_name = $request->name;
      $sector->sector_parent_id = $request->sector_parent_id;
      $sector->editor_id = $user->id;
      $sector->save();
    }

    if ($sector) {
      // Переадресовываем на index
      return redirect()->action('SectorController@get_content', ['item_id' => $sector->id]);
    } else {
      $result = [
        'error_status' => 1,
        'error_message' => 'Ошибка при записи сектора!'
      ];
    }
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
      $result = [
        'error_status' => 1,
        'error_message' => 'Данная область содержит населенные пункты, удаление невозможно'
      ];
      
    } else {
      // Если нет, мягко удаляем
      if ($sector->industry_status == 1) {
        $parent = null;
      } else {
        $parent = $sector->sector_parent_id;
      }

      $sector->editor_id = $user->id;
      $sector->save();

      // Если нет, мягко удаляем
      $sector = Sector::destroy($id);

      if ($sector) {
        // Переадресовываем на index
        return redirect()->action('SectorController@get_content', ['item_id' => $parent]);
      } else {
        $result = [
          'error_status' => 1,
          'error_message' => 'Ошибка при записи сектора!'
        ];
      }
    };
  }

  // Проверка наличия в базе
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

  // Список секторов
  public function sectors_list(Request $request)
  {
    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_dependence, 'index');

    // Главный запрос
    $sectors = Sector::moderatorLimit($answer)
    ->get(['id','sector_name','industry_status','sector_parent_id'])
    ->keyBy('id')
    ->toArray();

    // dd($sectors);

    // Формируем дерево вложенности
    $sectors_cat = [];
    foreach ($sectors as $id => &$node) { 

      // Если нет вложений
      if (!$node['sector_parent_id']) {
        $sectors_cat[$id] = &$node;
      } else { 

      // Если есть потомки то перебераем массив
        $sectors[$node['sector_parent_id']]['children'][$id] = &$node;
      };
    };

    // dd($sectors_cat);

    // Функция отрисовки option'ов
    function tplMenu($sector, $padding, $parent, $id) {

      // Убираем из списка пришедший пункт меню 
      if ($sector['id'] != $id) {

        $selected = '';
        if ($sector['id'] == $parent) {
          $selected = ' selected';
        }
        if ($sector['industry_status'] == 1) {
          $menu = '<option value="'.$sector['id'].'" class="first"'.$selected.'>'.$sector['sector_name'].'</option>';
        } else {
          $menu = '<option value="'.$sector['id'].'"'.$selected.'>'.$padding.' '.$sector['sector_name'].'</option>';
        }
        
        // Добавляем пробелы вложенному элементу
        if (isset($sector['children'])) {
          $i = 1;
          for($j = 0; $j < $i; $j++){
            $padding .= '&nbsp;&nbsp;&nbsp;&nbsp;';
          }     
          $i++;
          
          $menu .= showCat($sector['children'], $padding, $parent, $id);
        }
        return $menu;
      }
    }
    // Рекурсивно считываем наш шаблон
    function showCat($data, $padding, $parent, $id){
      $string = '';
      $padding = $padding;
      foreach($data as $item){
        $string .= tplMenu($item, $padding, $parent, $id);
      }
      return $string;
    }

    // Получаем HTML разметку
    $sectors_final = showCat($sectors_cat, '', $request->parent, $request->id);

    // Отдаем ajax
    echo json_encode($sectors_final, JSON_UNESCAPED_UNICODE);

    // dd($sectors_final);
  }

  // Сортировка
  public function sectors_sort(Request $request)
  {
    $result = '';
    $i = 1;
    foreach ($request->sectors as $item) {

      $sector = Sector::findOrFail($item);
      $sector->sort = $i;
      $sector->save();

      // if ($sector) {
      //   $result = [
      //     'error_status' => 0,
      //   ];
      // } else {
      //   $result = [
      //     'error_status' => 1,
      //     'msg' => 'Произошла непредвиденная ошибка, попробуйте перезагрузить страницу и попробуйте еще раз'
      //   ];
      // }
      // return json_encode($result, JSON_UNESCAPED_UNICODE);

      $i++;
    }
  }
}
