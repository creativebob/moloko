<?php

namespace App\Http\Controllers;

// Модели
use App\Area;
use App\City;

// Валидация
use Illuminate\Http\Request;

// Политика
use App\Policies\AreaPolicy;

// Общие классы
use Illuminate\Support\Facades\Log;

// Специфические классы

// На удаление
use Illuminate\Support\Facades\Auth;

class AreaController extends Controller
{
    // Сущность над которой производит операции контроллер
    protected $entity_name = 'areas';
    protected $entity_dependence = false;

    public function index()
    {
        //
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
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

        // Удаляем с обновлением
        // Находим область и район города
        $area = Area::withCount('cities')->moderatorLimit($answer)->find($id);
        $region_id = $area->region_id;

        // Подключение политики
        $this->authorize('delete', $area);

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        if ($area->cities_count > 0) {
            abort(403, 'Район не пустой!');
        } else {
            $area->editor_id = $user->id;
            $area->save();

            // Удаляем район с обновлением
            $area = Area::destroy($id);

            if ($area) {

                // Переадресовываем на index
                return redirect()->action('CityController@index', ['id' => $region_id]);
            } else {
                abort(403, 'Ошибка при удалении района!');
            }
        }
    }

    public function ajax_sort(Request $request)
    {

        // Если не пустой район
        if (isset($request->areas)) {

            $i = 1;

            foreach ($request->areas as $item) {
                Area::where('id', $item)->update(['sort' => $i]);
                $i++;
            }
        }

        // Если не пустой город
        if (isset($request->cities)) {

            $i = 1;

            foreach ($request->ciites as $item) {
                City::where('id', $item)->update(['sort' => $i]);
                $i++;
            }
        }
    }

    // Системная запись
    public function ajax_system(Request $request)
    {

        if ($request->action == 'lock') {
            $system = 1;
        } else {
            $system = null;
        }

        $page = Area::where('id', $request->id)->update(['system' => $system]);

        if ($page) {

            $result = [
                'error_status' => 0,
            ];
        } else {

            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при обновлении статуса системной записи!'
            ];
        }
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    // Отображение на сайте
    public function ajax_display(Request $request)
    {

        if ($request->action == 'hide') {
            $display = null;
        } else {
            $display = 1;
        }

        $area = Area::where('id', $request->id)->update(['display' => $display]);

        if ($area) {

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
