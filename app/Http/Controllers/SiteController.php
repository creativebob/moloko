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
use Illuminate\Support\Facades\DB;

class SiteController extends Controller
{
  // Сущность над которой производит операции контроллер
  protected $entity_name = 'sites';
  protected $entity_dependence = false;

  public function index(Request $request)
  {
    // Получаем метод
    $method = __FUNCTION__;
    // Подключение политики
    $this->authorize($method, Site::class);
    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_dependence, $method);
    // -------------------------------------------------------------------------------------------
    // ГЛАВНЫЙ ЗАПРОС
    // -------------------------------------------------------------------------------------------
    $sites = Site::with('author', 'company')
    ->withoutGlobalScope($answer['moderator'])
    ->moderatorFilter($answer['dependence'])
    ->companiesFilter($answer['company_id'])
    ->filials($answer['filials'], $answer['dependence']) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
    ->authors($answer['all_authors'])
    ->systemItem($answer['system_item'], $answer['user_status'], $answer['company_id']) // Фильтр по системным записям
    ->orderBy('moderated', 'desc')
    ->paginate(30);
    // Инфо о странице
    $page_info = pageInfo($this->entity_name);
    return view('sites.index', compact('sites', 'page_info'));
  }

  public function create()
  {
    // Получаем метод
    $method = __FUNCTION__;
    // Подключение политики
    $this->authorize($method, Site::class);
    // Список меню для сайта
    $answer = operator_right('pages', $this->entity_dependence, $method);
    $menus = Menu::withoutGlobalScope($answer['moderator'])
    ->moderatorFilter($answer['dependence'])
    ->companiesFilter($answer['company_id'])
    ->filials($answer['filials'], $answer['dependence']) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
    ->authors($answer['all_authors'])
    ->systemItem($answer['system_item'], $answer['user_status'], $answer['company_id']) // Фильтр по системным записям
    ->whereNavigation_id(1) // Только для сайтов, разделы сайта
    ->get();
    $site = new Site;
    return view('sites.create', compact('site', 'menus'));  
  }

  public function store(SiteRequest $request)
  {
    // Получаем метод
    $method = 'create';
    // Подключение политики
    $this->authorize($method, Site::class);
    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_dependence, $method);

    // Получаем данные для авторизованного пользователя
    $user = $request->user();
    $user_id = $user->id;
    $user_status = $user->god;
    $company_id = $user->company_id;

    $site = new Site;
    $site->site_name = $request->site_name;
    $site->site_domen = $request->site_domen;
    $site_alias = explode('.', $request->site_domen);
    $site->site_alias = $site_alias[0];
    // Пишем ID компании авторизованного пользователя
    if($user->company_id == null) {
      abort(403, 'Необходимо авторизоваться под компанией');
    };
    $site->company_id = $company_id;
    $site->author_id = $user_id;
    $site->save();
    if ($site) {
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
    };
  }

  public function show($id)
  {
    //
  }

  public function edit($site_alias)
  {
    // Получаем метод
    $method = 'update';
    // ГЛАВНЫЙ ЗАПРОС:
    $site = Site::withoutGlobalScope(ModerationScope::class)->whereSite_alias($site_alias)->first();
    // Подключение политики
    $this->authorize($method, $site);
    // Список меню для сайта
    $answer = operator_right('sites', $this->entity_dependence, $method);
    $menus = Menu::withoutGlobalScope($answer['moderator'])
    ->moderatorFilter($answer['dependence'])
    ->companiesFilter($answer['company_id'])
    ->filials($answer['filials'], $answer['dependence']) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
    ->authors($answer['all_authors'])
    ->systemItem($answer['system_item'], $answer['user_status'], $answer['company_id']) // Фильтр по системным записям
    ->whereNavigation_id(1) // Только для сайтов, разделы сайта
    ->get();
    return view('sites.edit', compact('site', 'menus'));
  }

  public function update(SiteRequest $request, $id)
  {
    // Получаем метод
    $method = __FUNCTION__;
    // Получаем авторизованного пользователя
    $user = $request->user();
    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, true, $method);
    // ГЛАВНЫЙ ЗАПРОС:
    $site = Site::withoutGlobalScope($answer['moderator'])->findOrFail($id);
    // Подключение политики
    $this->authorize('update', $site);
    $site->site_name = $request->site_name;
    $site_alias = explode('.', $request->site_domen);
    $site->site_alias = $site_alias[0];
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
    };
  }

  public function destroy(Request $request, $id)
  {
    $user = $request->user();
    // ГЛАВНЫЙ ЗАПРОС:
    $site = Site::withoutGlobalScope(ModerationScope::class)->findOrFail($id);
    // Подключение политики
    $this->authorize('delete', $site);
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
    };
  }
   /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function sections($site_alias)
    { 
      // dd($site_alias);
      $site = Site::with('menus', 'author')->whereSite_alias($site_alias)->first();
      return view('sites.sections', compact('site'));
    }
}
