<?php

namespace App\Http\Controllers;

use App\Site;
use App\Page;
use App\Company;

use Illuminate\Http\Request;
use App\Http\Requests\UpdateSite;
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
    if (isset(Auth::user()->company_id)) {
      // Если у пользователя есть компания
      // $companies = Company::orderBy('company_name')->get()->pluck('company_name', 'id');
      $sites = Site::whereCompany_id(Auth::user()->company_id)->paginate(30);
    } else {
      if (Auth::user()->god == 1) {
        // Если нет, то бог без компании
        // $companies = Company::orderBy('company_name')->get()->pluck('company_name', 'id');
        $sites = Site::paginate(30);
      };
    };
    $page_info = Page::wherePage_alias('/sites')->whereSite_id('1')->first();
    $menu = Page::whereSite_id('1')->get();
    return view('sites', compact('sites', 'page_info', 'companies', 'menu'));
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
  public function store(Request $request)
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
      $error = 'ошибка';
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
    $site = Site::findOrFail($id);
    // $this->authorize('update', $site);

    $site->site_name = $request->site_name;
    $site->site_domen = $request->site_domen;
    $site->company_id = Auth::user()->company_id;
    
    $site->save();

    return Redirect('/sites');
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    // Удаляем сайт с обновлением
    $site = Site::destroy($id);
    if ($site) {
      return Redirect('/sites');
    } else {
      echo 'произошла ошибка';
    }; 
  }
}
