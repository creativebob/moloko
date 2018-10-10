<?php

namespace App\Http\Controllers;

use App\Feedback;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{

    // Сущность над которой производит операции контроллер
    protected $entity_name = 'feedback';
    protected $entity_dependence = false;

    public function index(Request $request)
    {

        // Включение контроля активного фильтра 
        $filter_url = autoFilter($request, $this->entity_name);
        if(($filter_url != null)&&($request->filter != 'active')){return Redirect($filter_url);};

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Feedback::class);

        // Получаем авторизованного пользователя
        $user = $request->user();

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // -------------------------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -------------------------------------------------------------------------------------------------------------------------

        $feedback = Feedback::moderatorLimit($answer)
        // ->filter($request, 'places_type_id', 'places_types')
        ->booklistFilter($request)
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->paginate(30);

        // ------------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА -------------------------------------------------------------------------------
        // ------------------------------------------------------------------------------------------------------------

        $filter_query = Feedback::moderatorLimit($answer)->get();

        // Создаем контейнер фильтра
        $filter['status'] = null;
        $filter['entity_name'] = $this->entity_name;
        $filter['inputs'] = $request->input();

        // $filter = addFilter($filter, $filter_query, $request, 'Тип помещения:', 'places_types', 'places_type_id', 'places_types', 'external-id-many');

        // Добавляем данные по спискам (Требуется на каждом контроллере)
        $filter = addBooklist($filter, $filter_query, $request, $this->entity_name);

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        return view('feedback.index', compact('feedback', 'page_info', 'filter', 'user'));
    }

    public function create(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Feedback::class);

        $feedback = new Feedback;

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        return view('feedback.create', compact('feedback', 'page_info'));
    }

    public function store(Request $request)
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Feedback::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // Получаем авторизованного пользователя
        $user = $request->user();
        $user_id = $user->id;

        $feedback = new Feedback;
        $feedback->person = $request->person;
        $feedback->job = $request->job;
        $feedback->body = $request->body;
        $feedback->call_date = $request->call_date;

        if($user->company_id != null){
            $feedback->company_id = $user->company_id;
        } else {
            $feedback->company_id = null;
        };

        $feedback->author_id = $user->id;

        // Если нет прав на создание полноценной записи - запись отправляем на модерацию
        if($answer['automoderate'] == false){
            $feedback->moderation = 1;
        };

        $feedback->save();
        return redirect('/admin/feedback');
    }


    public function show($id)
    {

        // Получаем авторизованного пользователя
        $user = $request->user();

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $place = Place::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize('update', $role);

        $role->name = $request->name;
        $role->description = $request->description;

        $role->save();

        return redirect('/admin/roles');

    }


    public function edit(Request $request, $id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $place = Place::with('places_types')->moderatorLimit($answer)->findOrFail($id);
        // dd($place->places_types);

        // Список типов помещений
        $places_types_query = PlacesType::get();

        $places_types = [];

        foreach ($place->places_types as $place_type){
            $places_types[] = $place_type->id;
        }

        // Имя столбца
        $column = 'places_types_id';
        $request[$column] = $places_types;

        // Контейнер для checkbox'а - инициируем
        $checkboxer['status'] = null;
        $checkboxer['entity_name'] = $this->entity_name;


        // Настраиваем checkboxer
        $places_types_checkboxer = addFilter(

            $checkboxer,                // Контейнер для checkbox'а
            $places_types_query,        // Коллекция которая будет взята
            $request,
            'Тип помещения',            // Название чекбокса для пользователя в форме
            'places_types',             // Имя checkboxa для системы
            'id',                       // Поле записи которую ищем
            'places_types', 
            'internal-self-one',        // Режим выборки через связи
            'checkboxer'                // Режим: checkboxer или filter

        );

        // Получаем список стран
        $countries_list = Country::get()->pluck('name', 'id');

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $place);

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        return view('places.edit', compact('place', 'page_info', 'places_types_checkboxer', 'countries_list'));
    }


    public function update(PlaceRequest $request, $id)
    {

        // Получаем авторизованного пользователя
        $user = $request->user();

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));


        // ГЛАВНЫЙ ЗАПРОС:
        $place = Place::with('location')->moderatorLimit($answer)->findOrFail($id);

        // dd($place);

        // Подключение политики
        $this->authorize('update', $place);

        // Пишем локацию
        $location = $place->location;
        if($location->city_id != $request->city_id) {
            $location->city_id = $request->city_id;
            $location->editor_id = $user_id;
            $location->save();
        }
        if($location->address != $request->address) {
            $location->address = $request->address;
            $location->editor_id = $user_id;
            $location->save();
        }
        if($location->country_id != $request->country_id) {
            $location->country_id = $request->country_id;
            $location->editor_id = $user_id;
            $location->save();
        }

        $place->name = $request->name;
        $place->description = $request->description;
        $place->square = $request->square;

        $place->save();

        // Если запись удачна - будем записывать связи
        if($place){

            // Записываем связи: id-шники в таблицу Rooms
            $result = $place->places_types()->sync($request->places_types_id);

        } else {
            abort(403, 'Ошибка записи помещения');
        };

        return redirect('/admin/places');

    }


    public function destroy(Request $request, $id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $feedback = Feedback::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $feedback);

        // Удаляем пользователя с обновлением
        $feedback = Feedback::moderatorLimit($answer)->where('id', $id)->delete();

        if($feedback) {return redirect('/admin/feedback');} else {abort(403,'Что-то пошло не так!');};
    }

    // Сортировка
    public function ajax_sort(Request $request)
    {

        $i = 1;

        foreach ($request->feedback as $item) {
            Feedback::where('id', $item)->update(['sort' => $i]);
            $i++;
        }
    }

    // Системная запись
    public function ajax_system_item(Request $request)
    {

        if ($request->action == 'lock') {
            $system = 1;
        } else {
            $system = null;
        }

        $item = Feedback::where('id', $request->id)->update(['system_item' => $system]);

        if ($item) {

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

        $item = Feedback::where('id', $request->id)->update(['display' => $display]);

        if ($item) {

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
