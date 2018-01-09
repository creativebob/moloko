<?php

namespace App\Http\Controllers;


use App\Page;
use App\Site;
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
      if (isset(Auth::user()->company_id)) {
        // Если у пользователя есть компания
        $company_id = Auth::user()->company_id;
        $pages = Page::whereHas('site', function ($query) {
                  $query->whereCompany_id(Auth::user()->company_id);
                })
                ->with('site')
                ->siteId($request->site_id)
                ->paginate(30);
      } else {
        // Если нет, то бог без компании
        if (Auth::user()->god == 1) {
          $pages = Page::siteId($request->site_id)->paginate(30);
        };
      }
      // Пишем сайт в сессию
      session(['current_site' => $request->site_id]);

      $page_info = Page::wherePage_alias('/pages')->whereSite_id('1')->first();

      return view('pages.index', compact('pages', 'page_info'));
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {   
      $sites = Site::whereCompany_id(Auth::user()->company_id)->get()->pluck('site_name', 'id');
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
    public function store(Request $request)
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
     
      

      return redirect('/pages?site_id=' . $request->site_id);
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
      $page = Page::findOrFail($id);
      $sites = Site::whereCompany_id(Auth::user()->company_id)->get()->pluck('site_name', 'id');
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
    public function update(Request $request, $id)
    {
      $page = Page::findOrFail($id);
  
      $page->page_name = $request->page_name;
      $page->page_title = $request->page_title;
      $page->page_description = $request->page_description;
      $page->page_alias = $request->page_alias;
      $page->site_id = $request->site_id;
      
      $page->save();

      return redirect('/pages?site_id=' . $request->site_id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    { 
      $page = Page::find($id);
      $site_id = $page->site_id;
      // Удаляем страницу с обновлением
      $page = Page::destroy($id);
      if ($page) {
        return Redirect('/pages?site_id=' . $site_id);
      } else {
        echo 'произошла ошибка';
      }; 
    }


}
