<?php

namespace App\Http\Controllers;


use App\Area;
use App\City;
use Illuminate\Http\Request;

class AreaController extends Controller
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
      // Проверяем содержит ли район вложенные населнные пункты
      $cities = City::where('area_id', '=', $id)->first();
      if ($cities) {
        // Если содержит, то даем сообщенеи об ошибке
        $data = [
          'status' => 0,
          'msg' => 'Данный район содержит населенные пункты, удаление невозможно'
        ];
      } else {
        // Если нет, мягко удаляем
        $area = Area::destroy($id);
        if ($area) {
          $data = [
            'status'=> 1,
            'type' => 'areas',
            'id' => $id,
            'msg' => 'Успешно удалено'
          ];
        } else {
          // В случае непредвиденной ошибки
          $data = [
            'status' => 0,
            'msg' => 'Произошла непредвиденная ошибка, попробуйте перезагрузить страницу и попробуйте еще раз'
          ];
        };
      };
      echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }
}
