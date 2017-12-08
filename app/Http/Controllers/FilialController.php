<?php

namespace App\Http\Controllers;


use App\Filial;
use App\City;
use App\Department;

use Illuminate\Http\Request;

class FilialController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $filials = Filial::withCount('departmens');
      $cities = City::all();
      return view('filials', compact('filials', 'cities'));
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
      $filial_database = $request->filial_database;
      // По умолчанию значение 0
      if ($filial_database == 0) {
        // Проверка города в нашей базе данных
        $city_name = $request->city_name;

        $cities = City::where('city_name', 'like', $city_name . '%')->get();
        if ($cities) {
          $result = [
            'error_status' => 0,
            'cities' => $cities,
            // 'count' => $cities->count
          ];
        } else {
          $result = [
            'error_message' => 'Населенный пункт не существует в нашей базе данных, добавьте его!',
            'error_status' => 1
          ];
        };

        echo json_encode($result, JSON_UNESCAPED_UNICODE);
      };
      // Если город не найден, то меняем значение на 1, пишем в базу и отдаем результат
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
