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

    $menus = Menu::moderatorLimit($answer_menus)
    ->companiesLimit($answer_menus)
    ->filials($answer_menus) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
    ->authors($answer_menus)
    ->systemItem($answer_menus) // Фильтр по системным записям
    ->whereNavigation_id(1) // Только для сайтов, разделы сайта
    ->get();

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
    $user_id = $user->id;
    $user_status = $user->god;
    $company_id = $user->company_id;

    // Наполняем сущность данными
    $site = new Site;
    $site->name = $request->site_name;
    $site->domen = $request->site_domen;
    $site_alias = explode('.', $request->site_domen);
    $site->alias = $site_alias[0];
    $site->api_token = str_random(60);

    // Если нет прав на создание полноценной записи - запись отправляем на модерацию
    if($answer['automoderate'] == false){
      $user->moderation = 1;
    };
    // Пишем ID компании авторизованного пользователя
    if($user->company_id == null) {
      abort(403, 'Необходимо авторизоваться под компанией');
    };
    $site->company_id = $company_id;
    $site->author_id = $user_id;
    $site->save();
    if ($site) {

      // Создаем папку в файловой системе
      Storage::disk('public')->makeDirectory($site->company->company_alias.'/media/news');

      $mass = [];
      // Смотрим список пришедших разделов
      foreach ($request->menus as $menu) {
        $mass[] = [
          'site_id' => $site->id,
          'menu_id' => $menu,
          'author_id' => $user->id,
        ];
      };
      DB::table('menu_site')->insert($mass);
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
        return Site::with(['company', 'pages', 'navigations.menus.page', 'navigations.menus' => function ($query) {
          $query->orderBy('sort', 'asc');
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

    $menus = Menu::moderatorLimit($answer_menu)
    ->companiesLimit($answer_menu)
    ->filials($answer_menu) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
    ->authors($answer_menu)
    ->systemItem($answer_menu) // Фильтр по системным записям
    ->whereNavigation_id(1) // Только для сайтов, разделы сайта
    ->get();

    // Инфо о странице
    $page_info = pageInfo($this->entity_name);

    // dd($site);

    return view('sites.edit', compact('site', 'menus', 'page_info'));
  }

  public function update(SiteRequest $request, $id)
  {
    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

    // ГЛАВНЫЙ ЗАПРОС:
    $site = Site::moderatorLimit($answer)->findOrFail($id);

    // Подключение политики
    $this->authorize('update', $site);

    // Получаем авторизованного пользователя
    $user = $request->user();

    $site->name = $request->site_name;
    $site_alias = explode('.', $request->site_domen);
    $site->domen = $request->site_domen;
    $site->alias = $site_alias[0];
    // $site->company_id =  $user->company_id;
    $site->editor_id = $user->id;
    $site->save();

    if ($site) {
      // Когда сайт обновился, смотрим пришедние для него разделы
      if (isset($request->menus)) {
        $delete = MenuSite::whereSite_id($id)->delete();
        $mass = [];
        // Смотрим список пришедших роллей
        foreach ($request->menus as $menu) {
          $mass[] = [
            'site_id' => $site->id,
            'menu_id' => $menu,
            'author_id' => $user->id,
          ];
        };
        DB::table('menu_site')->insert($mass);
      } else {
        // Если удалили последний раздел для сайта и пришел пустой массив
        $delete = MenuSite::whereSite_id($id)->delete();
      };
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

    $user = $request->user();


    if ($site) {
      $site->editor_id = $user->id;
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
}
