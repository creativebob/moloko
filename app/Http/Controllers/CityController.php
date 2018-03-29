<?php

namespace App\Http\Controllers;

// Подключаем модели
use App\Region;
use App\Area;
use App\City;
use App\Page;
// Политика
use App\Policies\CityPolicy;
use App\Policies\AreaPolicy;
use App\Policies\RegionPolicy;
// Подключаем фасады
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
// Валидация
use App\Http\Requests\CityRequest;

class CityController extends Controller
{
  // Сущность над которой производит операции контроллер
  protected $entity_name = 'cities';
  protected $entity_dependence = false;

  public function index(Request $request)
  {

    // Подключение политики
    $this->authorize(getmethod(__FUNCTION__), Region::class);
    $this->authorize(getmethod(__FUNCTION__), Area::class);
    $this->authorize(getmethod(__FUNCTION__), City::class);

    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

    // -------------------------------------------------------------------------------------------
    // ГЛАВНЫЙ ЗАПРОС
    // -------------------------------------------------------------------------------------------
    $regions = Region::with(['areas'  => function ($query) {
      $query->orderBy('sort', 'asc');
    }, 'areas.cities' => function ($query) {
      $query->orderBy('sort', 'asc');
    }, 'cities' => function ($query) {
      $query->orderBy('sort', 'asc');
    }])
    ->moderatorLimit($answer)
      // ->companiesLimit($answer['company_id']) нет фильтра по компаниям
      ->filials($answer) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
      ->authors($answer)
      ->systemItem($answer) // Фильтр по системным записям
      ->orderBy('sort', 'asc')
      ->get();

    // Инфо о странице
      $page_info = pageInfo($this->entity_name);

      return view('cities.index', compact('regions', 'page_info')); 
    }


    public function get_content(Request $request)
    {
   // Подключение политики
      $this->authorize(getmethod('index'), Region::class);
      $this->authorize(getmethod('index'), Area::class);
      $this->authorize(getmethod('index'), City::class);

    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
      $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod('index'));

    // -------------------------------------------------------------------------------------------
    // ГЛАВНЫЙ ЗАПРОС
    // -------------------------------------------------------------------------------------------
      $regions = Region::with(['areas'  => function ($query) {
        $query->orderBy('sort', 'asc');
      }, 'areas.cities' => function ($query) {
        $query->orderBy('sort', 'asc');
      }, 'cities' => function ($query) {
        $query->orderBy('sort', 'asc');
      }])
      ->moderatorLimit($answer)
      // ->companiesLimit($answer['company_id']) нет фильтра по компаниям
      ->filials($answer) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
      ->authors($answer)
      ->systemItem($answer) // Фильтр по системным записям
      ->orderBy('sort', 'asc')
      ->get();

     // Отдаем Ajax
      return view('cities.cities-list', ['regions' => $regions, 'id' => $request->id]);
    }

    public function create()
    {
    //
    }

  public function store(CityRequest $request)
  {
    if ($request->city_db == 1) {

      // Подключение политики
      $this->authorize(getmethod(__FUNCTION__), Region::class);
      $this->authorize(getmethod(__FUNCTION__), Area::class);
      $this->authorize(getmethod(__FUNCTION__), City::class);

      // Получаем данные для авторизованного пользователя
      $user = $request->user();
      if ($user->god == 1) {
        $user_id = 1;
      } else {
        $user_id = $user->id;
      }
      // $company_id = $user->company_id;
      // $filial_id = $user->filial_id;

      // Если пришел город
      if (isset($request->region_name)) {

        // Вносим пришедшие данные в переменные
        $region_name = $request->region_name;
        $area_name = $request->area_name;
        $city_name = $request->city_name;

        // Смотрим область
        $region = Region::where('region_name', $region_name)->first();
        if ($region) {
          // Если существует, берем id существующий
          $region_id = $region->id;
        } else {
          if ($region_name == null) {
          // Если области нет
            $region_id = 0;
          } else {
            // Записываем новую область
            $region = new Region;
            $region->region_name = $region_name;
            $region->author_id = $user_id;
            $region->system_item = 1;
            $region->save();
            // Берем id записанной области
            $region_id = $region->id;
          }
        }
        // Смотрим район
        $area = Area::where('area_name', $area_name)->first();
        if ($area) {
          // Если существует, берем id существующей
          $area_id = $area->id;
        } else {
          if ($area_name == null) {
            $area_id = 0;
          } else {
            // Записываем новый район
            $area = new Area;
            $area->area_name = $area_name;
            $area->region_id = $region_id;
            $area->author_id = $user_id;
            $area->system_item = 1;
            $area->save();
            // Берем id записанного района
            $area_id = $area->id;
          }
        }

        // Записываем город, его наличие в базе мы проверили ранее
        $city = new City;
        $city->city_name = $city_name;
        $city->city_code = $request->city_code;

        // Если у города нет района
        if ($area_id != 0) {
          $city->area_id = $area_id;
        } else {
          $city->region_id = $region_id;
        }
        $city->city_vk_external_id = $request->item_vk_external_id;
        $city->author_id = $user->id;
        $city->system_item = 1;
        $city->save();
        $item_id = $city->id;
      } else {

        // Если пришел город без области (Москва, Питер)
        $region = new Region;
        $region->region_name = $request->city_name;
        $region->region_vk_external_id = $request->item_vk_external_id;
        $region->author_id = $user_id;
        $region->system_item = 1;
        $region->save();
        $item_id = $region->id;
      }

      if (isset($item_id)) {
        // Переадресовываем на index
        return redirect()->action('CityController@get_content', ['id' => $item_id]);
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
      };
      $city = City::destroy($id);
      if ($city) {
        return redirect()->action('CityController@get_content', ['id' => $parent]);
      } else {
        abort(403, 'Ошибка при удалении!');
      };    
    } else {
      abort(403, 'Населенный пункт не найден в базе данных!');
    };
  }

  // Получаем список городов из базы вк
  public function get_vk_city(CityRequest $request)
  {
    // Отправляем запров вк
    $city = $request->city;
    $request_params = [
      'country_id' => '1',
      'q' => $city,
      'need_all' => '0',
      'count' => '250',
      'v' => '5.71'
    ];
    $get_params = http_build_query($request_params);
    $result = (file_get_contents('https://api.vk.com/method/database.getCities?'. $get_params));

    // dd($result);

    // Если чекбокс не включен, то выдаем результат только по нашим областям
    if ($request->checkbox == 'false') {

      // Выбираем все наши области
      $regions = Region::select('region_name')->get();

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
              if ($region_name == $region->region_name) {
 
                $answer->response->items[] = (object) [
                  'region' => $region_name,
                  'area' => $area_name,
                  'title' => $title,
                  'id' => $id,
                ];
              }
            }
          } 
          // else {
          //   $answer->response->items[] = (object) [
          //     'region' => $title,
          //     'id' => $id,
          //   ];
          //   break;
          // }
        }

        // dd($my_count);
        
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
  public function cities_list(CityRequest $request)
  {

    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
      $answer = operator_right($this->entity_name, $this->entity_dependence, 'index');

    // Проверка города в нашей базе данных
      $city_name = $request->city_name;

      $cities = City::moderatorLimit($answer)->where('city_name', 'like', $city_name.'%')->get();
      $count = $cities->count();
      if ($count > 0) {
        $objRes = (object) [];
        foreach ($cities as $city) {
          $city_id = $city->id;
          $city_name = $city->city_name;
          if ($city->area_id == null) {
            $area_name = '';
            $region_name = $city->region->region_name;
          } else {
            $area_name = $city->area->area_name;
            $region_name = $city->area->region->region_name;
          };
          $objRes->city_id[] = $city_id;
          $objRes->city_name[] = $city_name;
          $objRes->area_name[] = $area_name;
          $objRes->region_name[] = $region_name;
        };
        $result = [
          'error_status' => 0,
          'cities' => $objRes,
          'count' => $count
        ];
      } else {
        $result = [
          'error_message' => 'Населенный пункт не существует в нашей базе данных, добавьте его!',
          'error_status' => 1
        ];
      };
      echo json_encode($result, JSON_UNESCAPED_UNICODE);
    // echo $request->city_name;
    }

  // Проверяем наличие города в базе
  public function city_check(CityRequest $request)
  {
    if (isset($request->city_name)) {
      if (isset($request->area_name)) {
        // Если район существует
        $city_name = $request->city_name;
        $area = Area::with(['cities' => function($query) use ($city_name) {
          $query->where('city_name', $city_name);
        }])->where('area_name', $request->area_name)->first();
        // Если в районе существует город, даем ошибку
        if ($area) {
          $result = [
            'error_status' => 1,
            'item' => $area
          ];
        } else {
          $result = [
            'error_status' => 0
          ];
        }
      } else {
        // Если город без района
        $city = City::where('city_name', $request->city_name)->first();
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

      $region = Region::where('region_name', $request->region_name)->first();
      if ($region) {
        $result = [
          'error_status' => 1,
        ];
      } else {
        $result = [
          'error_status' => 0
        ];
      }
    }
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
  }

// Сортировка
  public function cities_sort(Request $request)
  {
    $i = 1;
    foreach ($request->cities as $item) {

      $city = City::findOrFail($item);
      $city->sort = $i;
      $city->save();

      $i++;
    }
  }
}
