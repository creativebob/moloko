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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
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

    // Получаем список городов из нашей базы
    public function cities_list(Request $request)
    {

        // Подключение политики
//        $this->authorize('index', $this->class);

//        $answer = operator_right($this->entity_alias, $this->entity_dependence, 'index');

        // Проверка города в нашей базе данных
        $cities = City::with([
            'area:id,name',
            'region:id,name',
            'country:id,name'
        ])
//            ->moderatorLimit($answer)
            ->where('name', 'like', $request->name.'%')
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
