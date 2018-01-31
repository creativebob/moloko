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

// Подключаем фасады
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SiteController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    $user = $request->user();
    if (isset($user->company_id)) {
      // Если у пользователя есть компания
      $sites = Site::with('author')->whereCompany_id($user->company_id)->paginate(30);
    } else {
      if ($user->god == 1) {
        // Если нет, то бог без компании
        $sites = Site::with('author')->paginate(30);
      };
    };
    $page_info = pageInfo('sites');
    return view('sites.index', compact('sites', 'page_info'));
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
      $site = new Site;
      $menus = Menu::whereNavigation_id(1)->get();
      return view('sites.create', compact('site', 'menus'));  
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(SiteRequest $request)
  {
    $user = $request->user();
    $site = new Site;
    $site->site_name = $request->site_name;
    $site->site_domen = $request->site_domen;
    $site_alias = explode('.', $request->site_domen);
    $site->site_alias = $site_alias[0];
    // Пишем ID компании авторизованного пользователя
    if($user->company_id == null){abort(403, 'Необходимо авторизоваться под компанией');};
    $site->company_id = $user->company_id;
    $site->author_id = $user->id;
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

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
      //
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit($site_alias)
  {
    $site = Site::whereSite_alias($site_alias)->first();
    $menus = Menu::whereNavigation_id(1)->get();
    return view('sites.edit', compact('site', 'menus'));
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(SiteRequest $request, $id)
  {
    $user = $request->user();
    $site = Site::findOrFail($id);
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

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy(Request $request, $id)
  {
    $user = $request->user();
    $site = Site::findOrFail($id);
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
