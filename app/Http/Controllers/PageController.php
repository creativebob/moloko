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
    public function index($site_alias)
    { 

      $user = Auth::user();
      $site = Site::whereSite_alias($site_alias)->first();
      $pages = Page::with('site', 'author')->whereSite_id($site->id)->paginate(30);
      // $pages = Page::with(['author', 'site' => function($query) use ($site_alias) {
      //                 $query->whereSite_alias($site_alias);
      //               }])->paginate(30);
      // $site = '';
      // foreach ($pages as $page) {
      //   $site = $page->site;
      //   break;
      // };
      $page_info = pageInfo('pages');
      // dd($page_info);
      return view('pages.index', compact('pages', 'site', 'page_info', 'site_alias'));
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, $site_alias)
    {   
      $user = Auth::user();
      $sites = Site::whereCompany_id($user->company_id);
      $sites_list = $sites->pluck('site_name', 'id');
      $current_site = Site::whereSite_alias($site_alias)->first();
      $page = new Page;
      return view('pages.create', compact('page', 'sites_list', 'current_site', 'site_alias'));  
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PageRequest $request, $site_alias)
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
        return redirect('/sites/'.$site_alias.'/pages');
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
    public function edit(Request $request, $site_alias, $page_alias)
    {
      $user = Auth::user();
      $sites = Site::whereCompany_id($user->company_id)->get();
      $sites_list = $sites->pluck('site_name', 'id');
      $current_site = Site::where('site_alias', $site_alias)->first();
      $page = Page::With('site')->wherePage_alias($page_alias)->first();
      // dd($current_site);
      return view('pages.edit', compact('page', 'sites_list', 'current_site', 'site_alias'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PageRequest $request, $site_alias, $id)
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
        return redirect('/sites/'.$site_alias.'/pages');
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
    public function destroy($site_alias, $id)
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
          return Redirect('/sites/'.$site_alias.'/pages');
        } else {
          abort(403, 'Ошибка при удалении страницы');
        };
      } else {
        abort(403, 'Страница не найдена');
      };
    }
}
