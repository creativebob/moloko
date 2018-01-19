<?php

namespace App\Http\Controllers;

// Подключаем модели
use App\Area;

// Подключаем фасады
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
      $user = Auth::user();
      // Удаляем с обновлением
      // Находим область и район города
      $area = Area::with('cities')->findOrFail($id);
      $region_id = $area->region_id;
      // dd($area);
      
      if (count($area->cities) > 0) {
        abort(403, 'Район не пустой!');
      } else {
        $area->editor_id = $user->id;
        $area->save();
        $area = Area::destroy($id);
        if ($area) {
          return Redirect('current_city/'.$region_id.'/0');
        } else {
          abort(403, 'Ошибка при удалении района!');
        }
      }
    }
}
