<?php

namespace App\Http\Controllers;

// Подключаем модели
use App\Menu;
use App\Page;
use App\Navigation;
use App\Site;
// Валидация
use App\Http\Requests\MenuRequest;
// Политика
use App\Policies\MenuPolicy;
// Подключаем фасады
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Session;

class MenuController extends Controller
{
  // Сущность над которой производит операции контроллер
  protected $entity_name = 'menus';
  protected $entity_dependence = false;

  public function index(Request $request, $site_alias)
  {

  }


  public function create()
  {

  }


  public function store(MenuRequest $request, $site_alias)
  {

    // Подключение политики
    $this->authorize(getmethod(__FUNCTION__), Menu::class);

    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

    // Получаем данные для авторизованного пользователя
    $user = $request->user();
    $user_id = $user->id;
    $user_status = $user->god;
    $company_id = $user->company_id;

    // Пишем раздел меню
    $menu = new Menu;
    $menu->menu_name = $request->menu_name;
    if (isset($request->menu_icon)) {
      $menu->menu_icon = $request->menu_icon;
    };
    if (isset($request->menu_alias)) {
      $menu->menu_alias = $request->menu_alias;
    };
    $menu->navigation_id = $request->navigation_id;
    $menu->menu_parent_id = $request->menu_parent_id;
    $menu->page_id = $request->page_id;
    // dd($menu->page_id = $request->page_id);
    $menu->company_id = $company_id;
    $menu->author_id = $user_id;
    $menu->save();

    // dd($menu);
    if ($menu) {
      return Redirect('/sites/'.$site_alias.'/current_navigation/'.$menu->navigation_id.'/'.$menu->id);
    } else {
      abort(403, 'Ошибка при записи раздела меню!');
    };
  }


  public function show($id)
  {
    
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit(Request $request, $site_alias, $id)
  {

    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

    // ГЛАВНЫЙ ЗАПРОС:
    $menu = Menu::moderatorLimit($answer)->findOrFail($id);

    // Подключение политики
    $this->authorize(getmethod(__FUNCTION__), $menu);
   
    $navigation = Navigation::with('menus')->moderatorLimit($answer)->findOrFail($menu->navigation_id);
    $menus = $navigation->menus->pluck('menu_name', 'id');

    $result = [
      'menu_name' => $menu->menu_name,
      'menu_icon' => $menu->menu_icon,
      'menu_alias' => $menu->menu_alias,
      'page_id' => $menu->page_id,
      'navigation_id' => $menu->navigation_id,
      'menu_parent_id' => $menu->menu_parent_id,
      'menus' => $menus,
    ];
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
  }


  public function update(MenuRequest $request, $site_alias, $id)
  {

    // Получаем авторизованного пользователя
    $user = $request->user();

    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

    // ГЛАВНЫЙ ЗАПРОС:
    $menu = Menu::with('navigation')->moderatorLimit($answer)->findOrFail($id);

    // Подключение политики
    $this->authorize(getmethod(__FUNCTION__), $menu);
    $site_id = $menu->navigation->site_id;
    $menu->menu_name = $request->menu_name;
    $menu->menu_alias = $request->menu_alias;
    $menu->navigation_id = $request->navigation_id;
    $menu->menu_icon = $request->menu_icon;
    $menu->menu_parent_id = $request->menu_parent_id;
    $menu->page_id = $request->page_id;
    $menu->editor_id = $user->id;
    $menu->save();

    // dd($menu);
    if ($menu) {
      return Redirect('/sites/'.$site_alias.'/current_navigation/'.$menu->navigation_id.'/'.$menu->id);
    } else {
      abort(403, 'Ошибка обновления раздела меню');
    };
  }


  public function destroy(Request $request, $site_alias, $id)
  {

    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

    // ГЛАВНЫЙ ЗАПРОС:
    $menu = Menu::with('navigation')->moderatorLimit($answer)->findOrFail($id);

    // Подключение политики
    $this->authorize(getmethod(__FUNCTION__), $menu);

    $user = $request->user();
    $navigation_id = $menu->navigation_id;
    $site_id = $menu->navigation->site_id;
    if (isset($menu->menu_parent_id)) {
      $parent_id = $menu->menu_parent_id;
    } else {
      $parent_id = 0;
    };

    if ($menu) {
      $menu->editor_id = $user->id;
      $menu->save();
      $menu = Menu::destroy($id);

     // Удаляем с обновлением
      if ($menu) {
<<<<<<< HEAD

        return Redirect('/sites/'.$site_alias.'/current_menu/'.$navigation_id.'/'.$parent_id);
=======
        return Redirect('/sites/'.$site_alias.'/current_navigation/'.$navigation_id.'/'.$parent_id);
>>>>>>> 943b9c96be91100362c9ec22a57d670aaeda1c64
      } else {
        abort(403, 'Ошибка при удалении меню');
      };
    } else {
      abort(403, 'Меню не найдено');
    };
  }
}
