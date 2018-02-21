<?php

namespace App\Http\Controllers;

use App\MenuSite;
use Illuminate\Http\Request;

class MenuSiteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        $sections = MenuSite::moderatorLimit($answer)->whereSite_id($id)->pluck('menu_id');
        // if (count($sections) > 0) {
        //     $mymass = [];
        //     for ($i=1; $i <= count($sections->toArray()); $i++) { 
        //         foreach ($sections->toArray() as $section) {
        //             $mymass[$i] = $section;
        //         }
                
        //     }
        //     // dd($mymass);
        //     $result = [
        //         'data' => $mymass,
        //         'error_status' => 0
        //     ];
        // } else {
        //     $result = [
        //         'error_message' => 'Ничего не найдено',
        //         'error_status' => 1
        //     ];
        // };
        echo $sections->toArray();
        
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
