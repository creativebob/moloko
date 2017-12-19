<?php

namespace App\Http\Controllers;

use App\Position;
use App\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PositionController extends Controller
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
        $positions = Position::whereCompany_id(Auth::user()->company_id)
                ->orWhereNull('company_id')
                ->paginate(30);
      } else {
        // Если нет, то бог без компании
        if (Auth::user()->god == 1) {
          $positions = Position::paginate(30);
        };
      }

      $page_info = Page::wherePage_alias('/positions')->whereSite_id('1')->first();
      $menu = Page::whereSite_id(1)->get();
      return view('positions.index', compact('positions', 'page_info', 'menu'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      $menu = Page::whereSite_id('1')->get();
      $pages = Page::whereSite_id('1')->pluck('page_name', 'id');
      $position = new Position;
      return view('positions.create', compact('position', 'pages', 'menu'));  
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $position = new Position;

      $position->position_name = $request->position_name;
      $position->page_id = $request->page_id;
      $position->company_id = Auth::user()->company_id;
      
      $position->save();

      if ($position) {
        return Redirect('/positions');
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
    public function edit(Request $request, $id)
    {
      $position = Position::findOrFail($id);
      // $sites = Site::whereCompany_id(Auth::user()->company_id)->get()->pluck('site_name', 'id');
      $pages = Page::whereSite_id('1')->pluck('page_name', 'id');
      $menu = Page::whereSite_id('1')->get();
      return view('positions.edit', compact('position', 'menu', 'pages'));
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

      $position = Position::findOrFail($id);

      $position->position_name = $request->position_name;
      $position->page_id = $request->page_id;
      $position->company_id = Auth::user()->company_id;
      
      $position->save();

      if ($position) {
        return Redirect('/positions');
      } else {
        $error = 'ошибка';
      };

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
      // Удаляем страницу с обновлением
      $position = Position::destroy($id);
      if ($position) {
        return Redirect('/positions');
      } else {
        echo 'произошла ошибка';
      }; 
    }
}
