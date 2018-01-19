<?php

namespace App\Http\Controllers;

// Подключаем модели
use App\Page;
use App\Site;

// Валидация
use App\Http\Requests\PageRequest;

// Подключаем фасады
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    { 
      $user = Auth::user();
      if (isset($user->company_id)) {
        // Если у пользователя есть компания
        $pages = Page::whereSite_id($request->site_id)
                ->paginate(30);
        $site = Site::findOrFail($request->site_id);
      } else {
        // Если нет, то бог без компании
        if ($user->god == 1) {
          $pages = Page::whereSite_id($request->site_id)->paginate(30);
          $site = Site::findOrFail($request->site_id);
        };
      };
      // Пишем сайт в сессию
      session(['current_site' => $request->site_id]);
      $page_info = Page::wherePage_alias('/pages')->whereSite_id(1)->first();
      return view('pages.index', compact('pages', 'site', 'page_info'));
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {   
      $user = Auth::user();
      $sites = Site::whereCompany_id($user->company_id)->pluck('site_name', 'id');
      $current_site = $request->session()->get('current_site');
      $page = new Page;
      return view('pages.create', compact('page', 'sites', 'current_site'));  
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PageRequest $request)
    {
      $user = Auth::user();
      $page = new Page;
      $page->page_name = $request->page_name;
      $page->page_title = $request->page_title;
      $page->page_description = $request->page_description;
      $page->page_alias = $request->page_alias;
      $page->site_id = $request->site_id;
      $page->author_id = $user->id;
      $page->save();
      if ($page) {
        return redirect('/pages?site_id=' . $request->site_id);
      } else {
        abort(403, 'Ошибка при записи страницы!');
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
    public function edit(Request $request, $id)
    {
      $user = Auth::user();
      $sites = Site::whereCompany_id($user->company_id)->pluck('site_name', 'id');
      $page = Page::findOrFail($id);
      $current_site = $request->session()->get('current_site');
      return view('pages.edit', compact('page', 'sites', 'current_site'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PageRequest $request, $id)
    {
      $user = Auth::user();
      $page = Page::findOrFail($id);
      $page->page_name = $request->page_name;
      $page->page_title = $request->page_title;
      $page->page_description = $request->page_description;
      $page->page_alias = $request->page_alias;
      $page->site_id = $request->site_id;
      $page->editor_id = $user->id;
      $page->save();
      if ($page) {
        return redirect('/pages?site_id=' . $request->site_id);
      } else {
        abort(403, 'Ошибка при записи страницы!');
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
      $page = Page::findOrFail($id);
      $site_id = $page->site_id;
      if ($page) {
        $page->editor_id = $user->id;
        $page->save();
        // Удаляем страницу с обновлением
        $page = Page::destroy($id);
        if ($page) {
          return Redirect('/pages?site_id='.$site_id);
        } else {
          abort(403, 'Ошибка при удалении страницы');
        };
      } else {
        abort(403, 'Страница не найдена');
      };
    }
}
