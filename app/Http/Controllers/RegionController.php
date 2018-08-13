<?php

namespace App\Http\Controllers;

// Подключаем модели
use App\Region;
// Политика
use App\Policies\RegionPolicy;
// Подключаем фасады
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
// Валидация
use App\Http\Requests\RegionRequest;
use App\Http\Controllers\Session;

class RegionController extends Controller
{
  // Сущность над которой производит операции контроллер
  protected $entity_name = 'regions';
  protected $entity_dependence = false;

  public function index()
  {
    //
  }

  public function create()
  {
    //
  }
  /**
  * Добавляем регион в бд.
  */
  public function store(RegionRequest $request)
  {
    // Подключение политики
    
    $this->authorize('create', Region::class);

    // Получаем данные для авторизованного пользователя
    $user = $request->user();
    $user_id = $user->id;
    $user_status = $user->god;
    $company_id = $user->company_id;
    $filial_id = $request->filial_id;

    $region_database = $request->region_database;
    // По умолчанию значение 0
    if ($region_database == 0) {
      // Проверка области в нашей базе данных
      $region_name = $request->region_name;
      $regions = Region::moderatorLimit($answer)->whereRegion_name($region_name)->first();
      if ($regions) {
        $result = [
          'error_message' => 'Область уже добавлена в нашу базу!',
          'error_status' => 1
        ];
      } else {
        $result = [
          'region_database' => 1,
          'error_status' => 0,
          // 'session' => $lol
        ];
      }
      echo json_encode($result, JSON_UNESCAPED_UNICODE);
    };
    // Если область не найдена, то меняем значение на 1, пишем в базу и отдаем результат
    if ($region_database == 1) {
      $region = new Region;
      $region->region_name = $request->region_name;
      $region->region_code = $request->region_code;
      $region->region_vk_external_id = $request->region_vk_external_id;
      $region->author_id = $user_id;
      $region->system_item = 1;
      $region->save();

      if ($region) {
        $region_id = $region->id;
        $region = [
          'region_id' => $region_id,
          'region_name' => $region->region_name,
          'region_vk_external_id' => $region->region_vk_external_id
        ];
        echo json_encode($region, JSON_UNESCAPED_UNICODE);
      } else {
        abort(403, 'Не удалось записать область!');
      }
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
  /**
  * Удаляем регион из бд.
  */
  public function destroy(Request $request, $id)
  {
    $user = $request->user();
    // Удаляем ajax
    // Проверяем содержит ли область вложенные населенные пункты
    $region = Region::with('areas', 'cities')->moderatorLimit($answer)->findOrFail($id);
    // Подключение политики
    $this->authorize('delete', $region);
    if ((count($region->areas) > 0) || (count($region->cities) > 0)) {
      // Если содержит, то даем сообщение об ошибке
      $data = [
        'status' => 0,
        'msg' => 'Данная область содержит населенные пункты, удаление невозможно'
      ];
    } else {
      $region->editor_id = $user->id;
      $region->save();
      // Если нет, мягко удаляем
      $region = Region::destroy($id);
      if ($region){
        $data = [
          'status'=> 1,
          'type' => 'regions',
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
  /**
  * Получаем сторонние данные по области (из vk).
  */
  public function get_vk_region(RegionRequest $request)
  {
    $region = $request->region; 
    $request_params = [
    'country_id' => '1',
    'q' => $region,
    'count' => '100',
    'v' => '5.71'
    ];
    $get_params = http_build_query($request_params);
    $result = (file_get_contents('https://api.vk.com/method/database.getRegions?'. $get_params));
    echo $result;
  }

  public function regions_sort(Request $request)
  {
    $i = 1;

    foreach ($request->regions as $item) {
            Region::where('id', $item)->update(['sort' => $i]);
            $i++;
        }
  }

  // Отображение на сайте
    public function ajax_display(Request $request)
    {

        if ($request->action == 'hide') {
            $display = null;
        } else {
            $display = 1;
        }

        $region = Region::findOrFail($request->id);
        $region->display = $display;
        $region->save();

        if ($region) {

            $result = [
                'error_status' => 0,
            ];  
        } else {

            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при обновлении отображения на сайте!'
            ];
        }
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }
}
