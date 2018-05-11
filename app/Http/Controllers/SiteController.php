<?php

namespace App\Http\Controllers;

// Подключаем модели
use App\Site;
use App\Page;
use App\Menu;
use App\MenuSite;
use App\Company;

// Валидация
use App\Http\Requests\SiteRequest;

// Политика
use App\Policies\SitePolicy;

// Подключаем фасады
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SiteController extends Controller
{
  // Сущность над которой производит операции контроллер
  protected $entity_name = 'sites';
  protected $entity_dependence = false;

  public function index(Request $request)
  {

    // Подключение политики
    $this->authorize(getmethod(__FUNCTION__), Site::class);

    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));
    // -------------------------------------------------------------------------------------------
    // ГЛАВНЫЙ ЗАПРОС
    // -------------------------------------------------------------------------------------------
    $sites = Site::with('author', 'company')
    ->moderatorLimit($answer)
    ->companiesLimit($answer)
    ->filials($answer) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
    ->authors($answer)
    ->systemItem($answer) // Фильтр по системным записям
    ->orderBy('moderation', 'desc')
    ->paginate(30);

    // Инфо о странице
    $page_info = pageInfo($this->entity_name);

    return view('sites.index', compact('sites', 'page_info'));
  }


  public function create()
  {

    // Подключение политики
    $this->authorize(getmethod(__FUNCTION__), Site::class);

    // Список меню для сайта
    $answer_menus = operator_right('menus', false, 'index');

    $menus = Menu::whereNavigation_id(1) // Только для сайтов, разделы сайта
    ->get();
    // moderatorLimit($answer_menus)
    // ->companiesLimit($answer_menus)
    // ->filials($answer_menus) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
    // ->authors($answer_menus)
    // ->systemItem($answer_menus) // Фильтр по системным записям
    

    // dd($menus);

    $site = new Site;

    // Инфо о странице
    $page_info = pageInfo($this->entity_name);

    return view('sites.create', compact('site', 'menus', 'page_info'));  
  }


  public function store(SiteRequest $request)
  {
    // Подключение политики
    $this->authorize(getmethod(__FUNCTION__), Site::class);

    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

    // Получаем данные для авторизованного пользователя
    $user = $request->user();

    // Смотрим компанию пользователя
    $company_id = $user->company_id;
    if($company_id == null) {
      abort(403, 'Необходимо авторизоваться под компанией');
    }

    // Скрываем бога
    $user_id = hideGod($user);

    // Наполняем сущность данными
    $site = new Site;

    // Если нет прав на создание полноценной записи - запись отправляем на модерацию
    if($answer['automoderate'] == false){
      $site->moderation = 1;
    }

    // Cистемная запись
    $site->system_item = $request->system_item;

    $site->name = $request->site_name;
    $site->domen = $request->site_domen;
    $site_alias = explode('.', $request->site_domen);
    $site->alias = $site_alias[0];
    $site->api_token = str_random(60);
    $site->company_id = $company_id;
    $site->author_id = $user_id;
    $site->save();

    if ($site) {

      $sections = [];
      // Смотрим список пришедших разделов
      foreach ($request->menus as $menu) {
        $sections[] = [
          'site_id' => $site->id,
          'menu_id' => $menu,
          'author_id' => $user->id,
        ];
      };
      $site->menus()->attach($sections);
      return Redirect('/sites');
    } else {
      abort(403, 'Ошибка записи сайта');
    }
  }

  // Получаем сайт по api
  public function show(Request $request)
  {
    $site = Site::where('api_token', $request->token)->first();
    if ($site) {
      // return Cache::remember('site', 1, function() use ($domen) {
      return Site::with(['company.filials.city', 'company.city', 'pages', 'navigations.menus.page', 'navigations.navigations_category', 'navigations' => function ($query) {
        $query->whereDisplay(1);
      },'navigations.menus' => function ($query) {
        $query->whereDisplay(1)->orderBy('sort', 'asc');
      }])->whereDomen($request->domen)->orderBy('sort', 'asc')->first();
      // });
    } else {
      return json_encode('Нет доступа, холмс!', JSON_UNESCAPED_UNICODE);
    }
  }

  public function edit($alias)
  {

    // ГЛАВНЫЙ ЗАПРОС:
    $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));
    $site = Site::moderatorLimit($answer)->whereAlias($alias)->first();

    // Подключение политики
    $this->authorize(getmethod(__FUNCTION__), $site);

    // Список меню для сайта
    $answer_menu = operator_right('menus', false, 'index');
    $menus = Menu::whereNavigation_id(1) // Только для сайтов, разделы сайта
    ->get();

    // Инфо о странице
    $page_info = pageInfo($this->entity_name);

    // dd($site);

    return view('sites.edit', compact('site', 'menus', 'page_info'));
  }

  public function update(SiteRequest $request, $id)
  {
    // Получаем данные для авторизованного пользователя
    $user = $request->user();

    // Смотрим компанию пользователя
    $company_id = $user->company_id;
    if($company_id == null) {
      abort(403, 'Необходимо авторизоваться под компанией');
    }

    // Скрываем бога
    $user_id = hideGod($user);

    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

    // ГЛАВНЫЙ ЗАПРОС:
    $site = Site::moderatorLimit($answer)->findOrFail($id);

    // Подключение политики
    $this->authorize('update', $site);

    $site->name = $request->site_name;
    $site_alias = explode('.', $request->site_domen);
    $site->domen = $request->site_domen;
    $site->alias = $site_alias[0];
    $site->editor_id = $user_id;
    $site->save();

    if ($site) {
      // Когда сайт добавился, смотрим пришедние для него разделы
      if (isset($request->menus)) {
        $sections = [];
        // Формируем список пришедших разделов
        foreach ($request->menus as $menu) {
          $sections[] = [
            'site_id' => $site->id,
            'menu_id' => $menu,
            'author_id' => $user_id,
          ];
        };

        // Синхронизируем с существующими
        $site->menus()->sync($sections);
      } else {
        // Если удалили последний раздел для сайта и пришел пустой массив
        $delete = MenuSite::whereSite_id($id)->delete();
      }
      return Redirect('/sites');
    } else {
      abort(403, 'Ошибка обновления сайта');
    }
  }

  public function destroy(Request $request, $id)
  {
    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

    // ГЛАВНЫЙ ЗАПРОС:
    $site = Site::moderatorLimit($answer)->findOrFail($id);

    // Подключение политики
    $this->authorize(getmethod(__FUNCTION__), $site);

    if ($site) {
      // Получаем пользователя
      $user = $request->user();
      // Скрываем бога
      $user_id = hideGod($user);

      $site->editor_id = $user_id;
      $site->save();
      // Удаляем сайт с обновлением
      $site = Site::destroy($id);
      if ($site) {
        return Redirect('/sites');
      } else {
        abort(403, 'Ошибка при удалении сайта');
      };
    } else {
      abort(403, 'Сайт не найден');
    }
  }


  public function sections($alias)
  { 
    // ГЛАВНЫЙ ЗАПРОС:
    $answer = operator_right($this->entity_name, $this->entity_dependence, 'update');

    $site = Site::with('menus', 'author')->moderatorLimit($answer)->whereAlias($alias)->first();

      // Подключение политики
    $this->authorize('view', $site);

      // Инфо о странице
    $page_info = pageInfo($this->entity_name);

    return view('sites.sections', compact('site', 'page_info'));
  }

  // Проверка наличия в базе
  public function site_check(Request $request)
  {
    // Проверка навигации по сайту в нашей базе данных
    $site = Site::whereDomen($request->domen)->first();

    // Если такой сайт существует
    if ($site) {
      $result = [
        'error_status' => 1,
      ];
    // Если нет
    } else {
      $result = [
        'error_status' => 0,
      ];
    }
    return json_encode($result, JSON_UNESCAPED_UNICODE);
  }
}
