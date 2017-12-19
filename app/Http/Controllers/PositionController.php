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
        $positions = Position::whereCompany_id([Auth::user()->company_id, null])
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
      $position = new Position;
      return view('pages.create', compact('position', 'menu'));  
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
