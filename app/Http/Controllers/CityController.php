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
    $regions = Region::with('areas', 'areas.cities', 'cities')
      ->moderatorLimit($answer)
      // ->companiesLimit($answer['company_id']) нет фильтра по компаниям
      ->filials($answer) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
      ->authors($answer)
      ->systemItem($answer) // Фильтр по системным записям
      ->get();

    // Инфо о странице
    $page_info = pageInfo($this->entity_name);
    return view('cities.index', compact('regions', 'page_info')); 
  }


  // Получаем сторонние данные по 
  public function current_city($region, $area)
  {

    // Подключение политики
    $this->authorize('index', Region::class);
    $this->authorize('index', Area::class);
    $this->authorize('index', City::class);

    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right('regions', $this->entity_dependence, getmethod(__FUNCTION__));

    // -------------------------------------------------------------------------------------------
    // ГЛАВНЫЙ ЗАПРОС
    // -------------------------------------------------------------------------------------------
    $regions = Region::with('areas', 'areas.cities', 'cities')
      ->moderatorLimit($answer)
      // ->companiesLimit($answer['company_id']) нет фильтра по компаниям
      ->filials($answer) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
      ->authors($answer)
      ->systemItem($answer) // Фильтр по системным записям
      ->get();
    
    $data = [
      'region_id' => $region,
      'area_id' => $area,
    ];

    // Инфо о странице
    $page_info = pageInfo($this->entity_name);
    return view('cities.index', compact('regions', 'page_info', 'data')); 
  }

  public function create()
  {
    //
  }

  public function store(CityRequest $request)
  {

    // Подключение политики
    $this->authorize(getmethod(__FUNCTION__), Region::class);
    $this->authorize(getmethod(__FUNCTION__), Area::class);
    $this->authorize(getmethod(__FUNCTION__), City::class);

    // Получаем данные для авторизованного пользователя
    $user = $request->user();
    $user_id = $user->id;
    $user_status = $user->god;
    $company_id = $user->company_id;
    $filial_id = $request->filial_id;

    // Пишем город в бд
    $city_database = $request->city_database;

    // По умолчанию значение 0
    if ($city_database == 0) {

      // Проверка города и района в нашей базе данных
      $area_name = $request->area_name;
      $city_name = $request->city_name;

      // если город без района
      if ($area_name == null) {

        $cities = City::where('city_name', $city_name)->first();
        if ($cities) {
          $result = [
            'error_message' => 'Населенный пункт уже добавлен в нашу базу!',
            'error_status' => 1
          ];
        } else {
          $result = [
            'error_status' => 0
          ];
        };
      } else {

        // Если город с районом
        $area = Area::with('cities')->where('area_name', $area_name)->first();

        // Если район существует
        if ($area) {
          $cities = $area->cities->where('city_name', $city_name)->first();
          // Если в районе существует город, даем ошибку
          if ($cities) {
            $result = [
              'error_message' => 'Населенный пункт уже добавлен в нашу базу!',
              'error_status' => 1
            ];
          } else {
            $result = [
              'error_status' => 0
            ];
          };
        } else {
          // Если района нет, то записываем
          $result = [
            'error_status' => 0
          ];
        };
      }
      echo json_encode($result, JSON_UNESCAPED_UNICODE);
    };

    // Если город не найден, то меняем значение на 1, пишем в базу и отдаем результат
    if ($city_database == 1) {
      // Вносим пришедшие данные в переменные
      $region_name = $request->region_name;
      $area_name = $request->area_name;
      $city_name = $request->city_name;
      // Смотрим область
      $region = Region::where('region_name', '=', $region_name)->first();
      if ($region) {
        // Если существует, берем id существующий
        $region_id = $region->id;
      } else {
        if ($region_name == null) {
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
        };
      };
      // Смотрим район
      $area = Area::where('area_name', '=', $area_name)->first();
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
        };
      };
      // Если у города нет области
      // if (condition) {
      //   # code...
      // };
      // Если у города нет района
      if ($area_id == 0) {
        $city = new City;
        $city->city_name = $city_name;
        $city->city_code = $request->city_code;
        $city->region_id = $region_id;
        $city->city_vk_external_id = $request->city_vk_external_id;
        $city->author_id = $user_id;
        $city->system_item = 1;
        $city->save();
        $city_id = $city->id;
      };
      
      if ($region_id != 0 && $area_id != 0) {
        // Записываем город, его наличие в базе мы проверили ранее
        $city = new City;
        $city->city_name = $city_name;
        $city->city_code = $request->city_code;
        $city->area_id = $area_id;
        $city->city_vk_external_id = $request->city_vk_external_id;
        $city->author_id = $user->id;
        $city->system_item = 1;
        $city->save();
        $city_id = $city->id;
      };
      return Redirect('current_city/'.$region_id.'/'.$area_id);
    };
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
        return Redirect('current_city/'.$region_id.'/'.$area_id);
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
    $city = $request->city;
    $request_params = [
    'country_id' => '1',
    'q' => $city,
    'need_all' => '0',
    'count' => '100',
    'v' => '5.71'
    ];
    $get_params = http_build_query($request_params);
    $result = (file_get_contents('https://api.vk.com/method/database.getCities?'. $get_params));

    // Если чекбокс не включен, то выдаем результат только по нашим областям
    if ($request->checkbox == 'false') {
      $regions = Region::select('region_name')->get();
      $vk_cities = json_decode($result);
      $items = $vk_cities->response->items;
      $count = $vk_cities->response->count;
      $objRes = (object) [];
      if ($count == 0) {
        $objRes->count = 0;
      } else {
        $objRes = (object) [];
        // Находим наши области
        foreach ($regions as $region) {
          $region_name = $region->region_name;
          // Перебираем пришедшие с vk
          foreach ($items as $item) {
            $title = $item->title;
            $id = $item->id;
            //Если нет области
            if (empty($item->region)) {
              $region = null;
            } else {
              $region = $item->region;
            };
            // Если нет района
            if (empty($item->area)) {
              $area = null;
            } else {
              $area = $item->area;
            };
            // Если имена областей совпали, заносим в наш обьект с результатами
            if ($region_name == $region) {
              $objRes->region[] = $region;
              $objRes->area[] = $area;
              $objRes->title[] = $title;
              $objRes->id[] = $id;
            };
          };
        };
      }
      echo json_encode($objRes, JSON_UNESCAPED_UNICODE);
    } else {
      // Если чекбокс "искать везде" включен, отдаем данные, пришедшие с vk 
      echo $result;
    }
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
}
