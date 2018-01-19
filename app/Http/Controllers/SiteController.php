<?php

namespace App\Http\Controllers;

// Подключаем модели
use App\Site;
use App\Page;
use App\Company;

// Валидация
use App\Http\Requests\SiteRequest;

// Подключаем фасады
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SiteController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $user = Auth::user();
    if (isset($user->company_id)) {
      // Если у пользователя есть компания
      $sites = Site::whereCompany_id($user->company_id)->paginate(30);
    } else {
      if ($user->god == 1) {
        // Если нет, то бог без компании
        $sites = Site::paginate(30);
      };
    };
    $page_info = Page::wherePage_alias('/sites')->whereSite_id('1')->first();
    return view('sites', compact('sites', 'page_info', 'companies'));
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
      //
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(SiteRequest $request)
  {
    $user = Auth::user();
    $site = new Site;
    $site->site_name = $request->site_name;
    $site->site_domen = $request->site_domen;
    $site->company_id = $user->company_id;
    $site->author_id = $user->id;
    $site->save();
    if ($site) {
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
  public function edit($id)
  {
    // 
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
    // $this->authorize('update', $site);
    $user = Auth::user();
    $site = Site::findOrFail($id);
    $site->site_name = $request->site_name;
    $site->site_domen = $request->site_domen;
    $site->company_id =  $user->company_id;
    $site->editor_id = $user->id;
    $site->save();
    if ($site) {
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
  public function destroy($id)
  {
    $user = Auth::user();
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
}
