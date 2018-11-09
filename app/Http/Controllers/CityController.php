<?php

namespace App\Http\Controllers;

// Модели
use App\Region;
use App\Area;
use App\City;
use App\Page;

// Валидация
use Illuminate\Http\Request;
use App\Http\Requests\CityRequest;

// Политика
use App\Policies\CityPolicy;
use App\Policies\AreaPolicy;
use App\Policies\RegionPolicy;

// Специфические классы
use Transliterate;

// На удаление
use Illuminate\Support\Facades\Auth;

class CityController extends Controller
{

    // Сущность над которой производит операции контроллер
    protected $entity_name = 'cities';
    protected $entity_dependence = false;

    public function index(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), City::class);

        // Решили обьеденить проверку регионов, районов и городов только в города
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_cities = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // -------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -------------------------------------------------------------------------------------------
        $regions = Region::with(['areas'  => function ($query) {
            $query->orderBy('moderation', 'desc')
            ->orderBy('sort', 'asc');
        }, 'areas.cities' => function ($query) use ($answer_cities) {
            $query->orderBy('moderation', 'desc')
            ->orderBy('sort', 'asc');
        }, 'cities' => function ($query) use ($answer_cities) {
            $query->moderatorLimit($answer_cities)
            // ->authors($answer_cities)
            // ->systemItem($answer_cities) // Фильтр по системным записям
            ->orderBy('moderation', 'desc')
            ->orderBy('sort', 'asc');
        }])
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->get();

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        // Отдаем Ajax
        if ($request->ajax()) {
            return view('cities.cities-list', ['regions' => $regions, 'id' => $request->id]);
        }

        return view('cities.index', compact('regions', 'page_info'));
    }

    public function create()
    {
        //
    }

    public function store(CityRequest $request)
    {
        if ($request->city_db == 1) {

            // Подключение политики
            $this->authorize(getmethod(__FUNCTION__), City::class);

            // Получаем данные для авторизованного пользователя
            $user = $request->user();

            // Скрываем бога
            $user_id = hideGod($user);

            $city_name = $request->city_name;

            // Если пришла область
            if (isset($request->region_name)) {

                // Вносим пришедшие данные в переменные
                $region_name = $request->region_name;

                // Смотрим область
                $region = Region::where('name', $region_name)->first();

                if ($region) {

                    // Если существует, берем id существующий
                    $region_id = $region->id;
                } else {

                    // Записываем новую область
                    $region = new Region;
                    $region->name = $region_name;
                    $region->author_id = $user_id;
                    $region->system_item = 1;
                    $region->save();

                    if ($region) {

                        // Берем id записанной области
                        $region_id = $region->id;
                    } else {
                        $result = [
                            'error_status' => 1,
                            'error_message' => 'Ошибка при записи области!'
                        ];
                    }
                }
            } else {

                // Если пришел город без области (Москва, Питер)

                // Смотрим область
                $region = Region::where('name', 'Города Федерального значения')->first();
                if ($region) {

                    // Если существует, берем id существующий
                    $region_id = $region->id;
                } else {

                    // Записываем новую область
                    $region = new Region;
                    $region->name = 'Города Федерального значения';
                    $region->author_id = $user_id;
                    $region->system_item = 1;
                    $region->save();

                    if ($region) {

                        // Берем id записанной области
                        $region_id = $region->id;
                    } else {
                        $result = [
                            'error_status' => 1,
                            'error_message' => 'Ошибка при записи области!'
                        ];
                    }
                }
            }

            // Если пришел район
            if (isset($request->area_name)) {

                // Вносим пришедшие данные в переменные
                $area_name = $request->area_name;

                // Смотрим район
                $area = Area::where('name', $area_name)->first();
                if ($area) {

                    // Если существует, берем id существующей
                    $region_id = 0;
                    $area_id = $area->id;
                } else {

                    // Записываем новый район
                    $area = new Area;
                    $area->name = $area_name;
                    $area->region_id = $region_id;
                    $area->author_id = $user_id;
                    $area->system_item = 1;
                    $area->save();

                    if ($area) {

                        // Берем id записанного района
                        $region_id = 0;
                        $area_id = $area->id;
                    } else {
                        $result = [
                            'error_status' => 1,
                            'error_message' => 'Ошибка при записи района!'
                        ];
                    }
                }
            } else {
                $area_id = 0;
            }

            $count = City::whereName($city_name)->count();

            // Записываем город, его наличие в базе мы проверили ранее
            $city = new City;
            $city->name = $city_name;
            $city->code = $request->code;

            if ($count > 0) {
                $count = $count + 1;
                $city_name = $city_name . $count;
            }

            $city->alias = Transliterate::make($city_name, ['type' => 'url', 'lowercase' => true]);

            $city->vk_external_id = $request->vk_external_id;

            if ($region_id != 0) {
                $city->region_id = $region_id;
            }

            if ($area_id != 0) {
                $city->area_id = $area_id;
            }

            $city->author_id = $user_id;
            $city->system_item = 1;
            $city->save();

            if ($city) {
            // Переадресовываем на index
                return redirect()->action('CityController@index', ['id' => $city->id]);
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

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // Удаляем город с обновлением
        // Находим область и район города
        $user = $request->user();

        $city = City::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $city);

        if ($city->area_id != null) {
            $parent = $city->area_id;
        } else {
            $parent = $city->region_id;
        }

        if ($city) {
            $city->editor_id = $user->id;
            $city->save();

            // Смотрим район
            if (count($city->area) > 0) {
                $area_id = $city->area->id;
                $region_id = $city->area->region->id;
            } else {
                $area_id = 0;
                $region_id = $city->region->id;
            }

            $city = City::destroy($id);

            if ($city) {
                return redirect()->action('CityController@index', ['id' => $parent]);
            } else {
                abort(403, 'Ошибка при удалении!');
            }
        } else {
            abort(403, 'Населенный пункт не найден в базе данных!');
        }
    }

    // Получаем список городов из базы вк
    public function get_vk_city(CityRequest $request)
    {
        // Отправляем запров вк
        $city = $request->city;
        // $city = 'ангарск';
        // dd($city);

        $request_params = [
            'country_id' => '1',
            'q' => $city,
            'need_all' => '0',
            'count' => '250',
            'v' => '5.69',
            'access_token' => env('VK_API_TOKEN')
        ];
        $get_params = http_build_query($request_params);
        $result = (file_get_contents('https://api.vk.com/method/database.getCities?'. $get_params));

        // echo $result;
        // dd($result);

        // Если чекбокс не включен, то выдаем результат только по нашим областям
        if ($request->checkbox == 0) {

            // Выбираем все наши области
            $regions = Region::select('name')->get();

            // Декодим пришедшие данные
            $vk_cities = json_decode($result);
            $items = $vk_cities->response->items;
            $count = $vk_cities->response->count;

            // dd($count);

            $answer = (object) ['response' => (object) []];
            if ($count == 0) {
                $answer->response->count = 0;
            } else {

                // Перебираем пришедшие с vk
                foreach ($items as $item) {
                    $title = $item->title;
                    $id = $item->id;

                    // Если есть область
                    if (isset($item->region)) {
                        $region_name = $item->region;

                        // Если есть район
                        if (isset($item->area)) {
                            $area_name = $item->area;
                        } else {
                            $area_name = null;
                        }

                        // Находим наши области
                        foreach ($regions as $region) {
                            // dd($region);

                            // Если имена областей совпали, заносим в наш обьект с результатами
                            if ($region_name == $region->name) {

                                $answer->response->items[] = (object) [
                                    'region' => $region_name,
                                    'area' => $area_name,
                                    'title' => $title,
                                    'id' => $id,
                                ];
                            }
                        }
                    }
                }

                if (isset($answer->response->items)) {

                    // Если нашлись наши области в пришедших, считаем количество items
                    $answer->response->count = count($answer->response->items);
                } else {

                    // Если совпадений не нашлось
                    $answer->response->count = 0;
                }
            }
            $result = json_encode($answer, JSON_UNESCAPED_UNICODE);
        }
        echo $result;
    }

    // Получаем список городов из нашей базы
    public function cities_list(Request $request)
    {

        // Подключение политики
        $this->authorize('index', City::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, 'index');

        // Проверка города в нашей базе данных
        $cities = City::moderatorLimit($answer)->where('name', 'like', $request->city_name.'%')->get();
        // dd($cities);

        return view('includes.cities.cities_table', compact('cities'));


        // $count = $cities->count();

        // if ($count > 0) {
        //     $objRes = (object) [];
        //     foreach ($cities as $city) {
        //         $city_id = $city->id;
        //         $city_name = $city->name;

        //         if ($city->area_id == null) {
        //             $area_name = '';
        //             $region_name = $city->region->name;
        //         } else {
        //             $area_name = $city->area->name;
        //             $region_name = $city->area->region->name;
        //         }
        //         $objRes->city_id[] = $city_id;
        //         $objRes->city_name[] = $city_name;
        //         $objRes->area_name[] = $area_name;
        //         $objRes->region_name[] = $region_name;
        //     }
        //     $result = [
        //         'error_status' => 0,
        //         'cities' => $objRes,
        //         'count' => $count
        //     ];
        // } else {
        //     $result = [
        //         'error_message' => 'Населенный пункт не существует в нашей базе данных, добавьте его!',
        //         'error_status' => 1
        //     ];
        // }
        // echo json_encode($result, JSON_UNESCAPED_UNICODE);
        // echo $request->city_name;
    }

    // Проверяем наличие города в базе
    public function ajax_check(CityRequest $request)
    {
        $city_name = $request->city_name;

        if (isset($request->region_name)) {

            if (isset($request->area_name)) {

                // Если район существует
                $area = Area::with(['cities' => function($query) use ($city_name) {
                    $query->where('name', $city_name);
                }])->where('name', $request->area_name)->first();

                // Если в районе существует город, даем ошибку
                if ($area) {

                    if (count($area->cities) > 0) {
                        $result = [
                            'error_status' => 1,
                        ];
                    } else {
                        $result = [
                            'error_status' => 0
                        ];
                    }
                } else {
                    $result = [
                        'error_status' => 0
                    ];
                }
            } else {

            // Если город без района
                $city = City::where('name', $request->city_name)->first();

                if ($city) {
                    $result = [
                        'error_status' => 1,
                    ];
                } else {
                    $result = [
                        'error_status' => 0
                    ];
                }
            }
        } else {

            $region = Region::with(['cities' => function($query) use ($city_name) {
                $query->where('name', $city_name);
            }])->where('name', 'Города Федерального значения')->first();

            if ($region) {

                if (count($region->cities) > 0) {
                    $result = [
                        'error_status' => 1,
                    ];
                } else {
                    $result = [
                        'error_status' => 0
                    ];
                }
            } else {
                $result = [
                    'error_status' => 0
                ];
            }
        }
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }
}
