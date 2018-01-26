<?php

namespace App\Http\Controllers;

// Подключаем модели
use App\Navigation;
use App\Menu;
use App\Page;
use App\Site;

// Валидация
use App\Http\Requests\NavigationRequest;

// Подключаем фасады
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NavigationController extends Controller
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
          $navigations = Navigation::with('site')->where(['category_navigation_id' => 2, 'company_id' => $user->company_id])->paginate(30);
          $sites = Site::whereCompany_id($user->company_id)->pluck('site_name', 'id');
        } else {
          if (Auth::user()->god == 1) {
            // Если нет, то бог без компании
            $navigations = Navigation::with('site')->where('category_navigation_id', 2)->paginate(30);
            $sites = Site::pluck('site_name', 'id');
          };
        };
        $page_info = pageInfo('navigations');
        return view('navigations', compact('navigations', 'page_info', 'sites'));
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
    public function store(NavigationRequest $request)
    {
        $user = Auth::user();
        $navigation = new Navigation;
        $navigation->navigation_name = $request->navigation_name;
        $navigation->site_id = $request->site_id;
        $navigation->company_id = $user->company_id;
        $navigation->author_id = $user->id;
        $navigation->save();
        // Пишем сайт в сессию
        session(['current_site' => $request->site_id]);
        if ($navigation) {
          return Redirect('/current_menu/'.$navigation->id.'/0');
        } else {
          abort(403, 'Ошибка при записи навигации!');
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
        $navigation = Navigation::findOrFail($id);
        // Отдаем данные по навигации
        $result = [
          'navigation_name' => $navigation->navigation_name,
        ];
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(NavigationRequest $request, $id)
    {
        $user = Auth::user();
        $navigation = Navigation::findOrFail($id);
        $navigation->navigation_name = $request->navigation_name;
        $navigation->site_id = $request->site_id;
        $navigation->company_id = $user->company_id;
        $navigation->editor_id = $user->id;
        $navigation->save();
        // Пишем сайт в сессию
        session(['current_site' => $request->site_id]);
        if ($navigation) {
          return Redirect('/current_menu/'.$navigation->id.'/0');
        } else {
          abort(403, 'Ошибка при записи навигации!');
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
      $navigation = Navigation::findOrFail($id);
      $site_id = $navigation->site_id;
      if ($navigation) {
        $navigation->editor_id = $user->id;
        $navigation->save();
        // Удаляем навигацию с обновлением
        $navigation = Navigation::destroy($id);
        if ($navigation) {
          return Redirect('/menus?site_id='.$site_id);
        } else {
          abort(403, 'Ошибка при удалении навигации');
        };
      } else {
        abort(403, 'Навигация не найдена');
      };
    }
}
