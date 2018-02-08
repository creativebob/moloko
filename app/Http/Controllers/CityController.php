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
    // Получаем метод
    $method = __FUNCTION__;
    // Подключение политики
    $this->authorize($method, Region::class);
    $this->authorize($method, Area::class);
    $this->authorize($method, City::class);
    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right('regions', $this->entity_dependence, $method);
    // dd($answer);
    // -------------------------------------------------------------------------------------------
    // ГЛАВНЫЙ ЗАПРОС
    // -------------------------------------------------------------------------------------------
    $regions = Region::with('areas', 'areas.cities', 'cities')
      ->withoutGlobalScope($answer['moderator'])
      ->moderatorFilter($answer['dependence'])
      // ->companiesFilter($answer['company_id']) нет фильтра по компаниям
      ->filials($answer['filials'], $answer['dependence']) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
      ->authors($answer['all_authors'])
      ->systemItem($answer['system_item'], $answer['user_status'], $answer['company_id']) // Фильтр по системным записям
      ->get();
    // Инфо о странице
    $page_info = pageInfo($this->entity_name);
    return view('cities', compact('regions', 'page_info')); 
  }
  // Получаем сторонние данные по 
  public function current_city($region, $area)
  {
    // Подключение политики
    $this->authorize('index', Region::class);
    $this->authorize('index', Area::class);
    $this->authorize('index', City::class);
    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right('regions', $this->entity_dependence, $method);
    // dd($answer);
    // -------------------------------------------------------------------------------------------
    // ГЛАВНЫЙ ЗАПРОС
    // -------------------------------------------------------------------------------------------
    $regions = Region::with('areas', 'areas.cities', 'cities')
      ->withoutGlobalScope($answer['moderator'])
      ->moderatorFilter($answer['dependence'])
      // ->companiesFilter($answer['company_id']) нет фильтра по компаниям
      ->filials($answer['filials'], $answer['dependence']) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
      ->authors($answer['all_authors'])
      ->systemItem($answer['system_item'], $answer['user_status'], $answer['company_id']) // Фильтр по системным записям
      ->get();
    
    $data = [
      'region_id' => $region,
      'area_id' => $area,
    ];
    // Инфо о странице
    $page_info = pageInfo($this->entity_name);
    return view('cities', compact('regions', 'page_info', 'data')); 
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
  public function store(CityRequest $request)
  {
    // Получаем метод
    $method = 'create';
    // Подключение политики
    $this->authorize($method, Region::class);
    $this->authorize($method, Area::class);
    $this->authorize($method, City::class);
    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_dependence, $method);
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
          $area_id = null;
        } else {
          // Записываем новый район
          $area = new Area;
          $area->area_name = $area_name;
          $area->region_id = $region_id;
          $area->author_id = $user_id;
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
        $city->save();
        $city_id = $city->id;
      };
      return Redirect('current_city/'.$region_id.'/'.$area_id);
    };
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
  public function destroy(Request $request, $id)
  { 
    // Удаляем город с обновлением
    // Находим область и район города
    $user = $request->user();
    $city = City::withoutGlobalScope(ModerationScope::class)->findOrFail($id);
    // Подключение политики
    $this->authorize('delete', $city);
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
}
