<?php

namespace App\Http\Controllers\Api\v1;

use App\City;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CityController extends Controller
{

    // Настройки сконтроллера
    public function __construct(City $city)
    {
        $this->city = $city;
        $this->entity_alias = with(new City)->getTable();
        $this->entity_dependence = false;
        $this->class = City::class;
        $this->model = 'App\City';
        $this->type = 'modal';
    }

    /**
     * Отображение списка ресурсов.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cities = City::with([
            'area:id,name',
            'region:id,name',
            'country:id,name'
        ])
            ->get([
                'id',
                'name',
                'area_id',
                'region_id',
                'country_id'
            ]);
//         dd($cities);
        return response()->json($cities);
    }

    /**
     * Сохранение созданного ресурса в хранилище.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Отображение указанного ресурса.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Обновление указанного ресурса в хранилище.
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
     * Удаление указанного ресурса из хранилища.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    // Получаем список городов из нашей базы
    public function cities_list(Request $request)
    {

        // Поиск города в нашей базе данных
        $cities = City::with([
            'area:id,name',
            'region:id,name',
            'country:id,name'
        ])
            ->where('name', 'like', $request->name  . '%')
            ->get([
                'id',
                'name',
                'area_id',
                'region_id',
                'country_id'
            ]);
//         dd($cities);

        return response()->json($cities);
    }
}
