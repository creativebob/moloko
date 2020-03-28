<?php

namespace App\Http\Controllers;

// Модели
use App\Country;
use App\Region;
use App\Area;
use App\City;

// Валидация
use Illuminate\Http\Request;
use App\Http\Requests\System\CityRequest;

// Транслитерация
use Illuminate\Support\Str;

class CityController extends Controller
{

    // Настройки сконтроллера
    public function __construct(City $city)
    {
        $this->middleware('auth');
        $this->city = $city;
        $this->entity_alias = with(new City)->getTable();
        $this->entity_dependence = false;
        $this->class = City::class;
        $this->model = 'App\City';
        $this->type = 'modal';
    }

    public function index(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Решили обьеденить проверку регионов, районов и городов только в города
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_cities = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // -------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -------------------------------------------------------------------------------------------
        $regions = Region::with([
            'areas'  => function ($query) {
                $query->orderBy('moderation', 'desc')
                ->orderBy('sort', 'asc');
            },
            'areas.cities' => function ($query) use ($answer_cities) {
                $query->orderBy('moderation', 'desc')
                ->orderBy('sort', 'asc');
            },
            'cities_without_area' => function ($query) use ($answer_cities) {
                $query->moderatorLimit($answer_cities)
                // ->authors($answer_cities)
                // ->systemItem($answer_cities) // Фильтр по системным записям
                ->orderBy('moderation', 'desc')
                ->orderBy('sort', 'asc');
            }])
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->get();

        $count = 0;
        if ($regions->isNotEmpty()) {
            foreach ($regions as $region) {
                if ($region->cities_without_area->isNotEmpty()) {
                    $count += $region->cities_without_area->count();
                }
                if ($region->areas->isNotEmpty()) {
                    foreach ($region->areas as $area) {
                        if ($area->cities->isNotEmpty()) {
                            $count += $area->cities->count();
                        }
                    }
                }
            }
        }

        // Отдаем Ajax
        if ($request->ajax()) {
            return view('cities.cities_list', ['regions' => $regions, 'id' => $request->id, 'count' => $count]);
        }

        $filter = setFilter($this->entity_alias, $request, [
            'booklist'              // Списки пользователя
        ]);

        // Инфо о странице
        $page_info = pageInfo($this->entity_alias);

        return view('cities.index', compact('regions', 'page_info', 'count', 'filter'));
    }

    public function create()
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        $countries = Country::get();
        return view('cities.create', compact('countries'));
    }

    public function store(CityRequest $request)
    {

        // dd($request);
        if ($request->city_db == 1) {

            $country_id = $request->country_id;

            // Подключение политики
            $this->authorize(getmethod(__FUNCTION__), $this->class);

            // Если пришла область
            if (isset($request->region_name)) {

                // Смотрим область
                $region = Region::firstOrCreate([
                    'name' => $request->region_name,
                    'country_id' => $country_id
                ], [
                    'system' => true,
                    'author_id' => 1
                ]);
                $region_id = $region->id;
            } else {
                // Если пришел город без области (Москва, Питер)
                // Смотрим область
                $region = Region::firstOrCreate([
                    'name' => 'Города Федерального значения',
                    'country_id' => $country_id
                ], [
                    'system' => true,
                    'author_id' => 1
                ]);
                $region_id = $region->id;
            }

            // Если пришел район
            if (isset($request->area_name)) {

                // Смотрим район
                $area = Area::firstOrCreate([
                    'name' => $request->area_name,
                    'region_id' => $region_id,
                    'country_id' => $country_id
                ], [
                    'system' => true,
                    'author_id' => 1
                ]);
                // Берем id записанного района
                $area_id = $area->id;
            } else {
                $area_id = null;
            }

            // Записываем город, его наличие в базе мы проверили ранее
            $city = new City;

            $city->name = $request->city_name;

            // $city->code = $request->code;
            $city_name = $request->city_name;

            // Если городов больше одного, меняем алиас
            $count = City::whereName($city_name)->count();
            if ($count > 0) {
                $count++;
                $city_name .= $count;
            }

            $city->alias = Str::slug($city_name);
            $city->vk_external_id = $request->vk_external_id;

            $city->region_id = $region_id;
            $city->area_id = $area_id;
            $city->country_id = $country_id;

            $city->author_id = 1;
            $city->system = 1;
            $city->save();

            if ($city) {
                // Переадресовываем на index
                return redirect()->route('cities.index', ['id' => $city->id]);
            } else {
                $result = [
                    'error_status' => 1,
                    'error_message' => 'Ошибка при записи населенного пункта!'
                ];
            }
        }
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy(Request $request, $id)
    {
        //
    }

    // Получаем список городов из базы вк
    public function get_vk_city(CityRequest $request)
    {
        // Отправляем запров вк
	    $country = Country::find($request->country_id);
        $country_id = $country->vk_external_id;
	    $city = $request->city;
        // $city = 'ангарск';
        // dd($city);
        // dd($request);

        $request_params = [
            'country_id' => $country_id,
            'q' => $city,
            'need_all' => 0,
            'count' => 250,
            'v' => 5.92,
            'access_token' => config('app.vk_api_token')
        ];
        $get_params = http_build_query($request_params);
        $result = (file_get_contents('https://api.vk.com/method/database.getCities?'. $get_params));

        // echo $result;
        // dd($result);

        // Декодим пришедшие данные
        $answer = json_decode($result);
        $vk_cities = $answer->response;
        // dd($vk_cities);

        // Если чекбокс не включен, то выдаем результат только по нашим областям
        if ($request->search_all == 'false') {

            // Выбираем все наши области
            $regions = Region::select('name')->get();

            // Декодим пришедшие данные
            $items = $vk_cities->items;
            $count = $vk_cities->count;

            // dd($count);

            $vk_cities = (object) [];
            if ($count == 0) {
                $vk_cities->count = 0;
            } else {

                // Перебираем пришедшие с vk
                foreach ($items as $item) {

                    // Если есть область
                    if (isset($item->region)) {

                        // Находим наши области
                        foreach ($regions as $region) {

                            // Если имена областей совпали, заносим в наш обьект с результатами
                            if ($item->region == $region->name) {

                                $vk_cities->items[] = (object) [
                                    'region' => $item->region,
                                    'area' => isset($item->area) ? $item->area : null,
                                    'title' => $item->title,
                                    'id' => $item->id,
                                ];
                            }
                        }
                    }
                }

                if (isset($vk_cities->items)) {

                    // Если нашлись наши области в пришедших, считаем количество items
                    $vk_cities->count = count($vk_cities->items);
                } else {

                    // Если совпадений не нашлось
                    $vk_cities->count = 0;
                }
            }
        }

        return view('cities.tbody', compact('vk_cities'));
    }

    // Проверяем наличие города в базе
    public function ajax_check(CityRequest $request)
    {
        $city_name = $request->city_name;

        if (isset($request->region_name)) {

            if (isset($request->area_name)) {

                // Если район существует
                $areas_count = Area::whereHas('cities', function($query) use ($city_name) {
                    $query->where('name', $city_name);
                })
                ->where('name', $request->area_name)
                ->count();

                return response()->json($areas_count);
            } else {
                // Если город без района
                return response()->json(City::where('name', $request->city_name)->count());
            }
        } else {

            $regions_count = Region::whereHas('cities', function($query) use ($city_name) {
                $query->where('name', $city_name);
            })
            ->where('name', 'Города Федерального значения')
            ->count();

            return response()->json($regions_count);
        }
    }

    // Получаем список городов из нашей базы
    public function cities_list(Request $request)
    {

        // Подключение политики
        $this->authorize('index', $this->class);

        $answer = operator_right($this->entity_alias, $this->entity_dependence, 'index');

        // Проверка города в нашей базе данных
        $cities = City::with([
            'area:id,name',
            'region:id,name',
            'country:id,name'
        ])
        ->moderatorLimit($answer)
        ->where('name', 'like', $request->name.'%')
        ->get([
            'id',
            'name',
            'area_id',
            'region_id',
            'country_id'
        ]);
//         dd($cities);

//        return response()->json($cities);
        return view('cities.cities_table', compact('cities'));
    }

    public function test()
    {
        $city = City::
//        where('name', 'тет')
//        ->
        first([
            'id',
            'name'
        ]);
//        dd($city);

        $location = null;
        return view('test', compact('location'));
    }
}
