<?php

namespace App\Http\Controllers;

// Модели
use App\Place;
use App\Country;
use App\PlacesType;
use App\User;
use App\Page;
use App\Folder;
use App\Booklist;
use App\List_item;
use App\Worktime;
use App\Location;
use App\ScheduleEntity;


// Модели которые отвечают за работу с правами + политики
use App\Policies\PlacePolicy;
use App\Policies\PlacesTypePolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

// Запросы и их валидация
use Illuminate\Http\Request;
use App\Http\Requests\PlaceRequest;

// Прочие необходимые классы
use Illuminate\Support\Facades\Log;
// use Illuminate\Support\Facades\Storage;
// use Carbon\Carbon;
// use Illuminate\Support\Facades\DB;

class PlaceController extends Controller
{

    // Сущность над которой производит операции контроллер
    protected $entity_name = 'places';
    protected $entity_dependence = false;

    public function index(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Place::class);

        // Получаем авторизованного пользователя
        $user = $request->user();

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ---------------------------------------------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // ---------------------------------------------------------------------------------------------------------------------------------------------

        $places = Place::moderatorLimit($answer)
        ->filter($request, 'places_type_id', 'places_types')
        ->booklistFilter($request)
        ->orderBy('moderation', 'desc')
        ->paginate(30);


        // ---------------------------------------------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ----------------------------------------------------------------------------------------------------------------
        // ---------------------------------------------------------------------------------------------------------------------------------------------

        $filter_query = Place::with('places_types')->moderatorLimit($answer)->get();
        $filter['status'] = null;
        $filter = addFilter($filter, $filter_query, $request, 'Тип помещения:', 'places_types', 'places_type_id', 'places_types', 'external-id-many');

        // Добавляем данные по спискам (Требуется на каждом контроллере)
        $filter = addBooklist($filter, $filter_query, $request, $this->entity_name);

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        return view('places.index', compact('places', 'page_info', 'filter', 'user'));
    }

    public function create(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Place::class);

        $place = new Place;

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        // Подключение политики
        $this->authorize(getmethod('index'), PlacesType::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_places_types = operator_right('places_types', 'false', 'index');
        dd($answer_places_types);

        // Список типов помещений
        $places_types_query = PlacesType::moderatorLimit($answer_places_types)
        ->companiesLimit($answer_places_types)
        ->authors($answer_places_types)
        ->systemItem($answer_places_types) // Фильтр по системным записям
        ->template($answer_places_types) // Выводим шаблоны в список
        ->get();

        $filter['status'] = null;

        $places_types_checkboxer = addFilter($filter, $places_types_query, $request, 'Тип помещения', 'places_types', 'id', 'internal-self-one');

        // Получаем список стран
        $countries_list = Country::get()->pluck('name', 'id');

        return view('places.create', compact('place', 'page_info', 'places_types_checkboxer', 'countries_list'));
    }

    public function store(Request $request)
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Place::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // Получаем авторизованного пользователя
        $user = $request->user();
        $user_id = $user->id;

        // Пишем локацию
        $location = new Location;
        $location->country_id = $request->country_id;
        $location->city_id = $request->city_id;
        $location->address = $request->address;
        $location->author_id = $user_id;
        $location->save();

        if ($location) {
            $location_id = $location->id;
        } else {

            abort(403, 'Ошибка записи адреса');
        }

        $place = new Place;
        $place->name = $request->name;
        $place->description = $request->description;
        $place->square = $request->square;
        $place->location_id = $location_id;

        if($user->company_id != null){
            $place->company_id = $user->company_id;
        } else {
            $place->company_id = null;
        };

        $place->author_id = $user->id;

        // Если нет прав на создание полноценной записи - запись отправляем на модерацию
        if($answer['automoderate'] == false){
            $place->moderation = 1;
        };

        $place->save();

        // Если запись удачна - будем записывать связи
        if($place){

            // Записываем связи: id-шники в таблицу Rooms
            if(isset($request->places_types)){
                $result = $place->places_types()->sync($request->places_types);               
            } else {
                $result = $place->places_types()->detach(); 
            };

        } else {
            abort(403, 'Ошибка записи помещения');
        };

        return redirect('/admin/places');
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


        // Подключение политики
        $this->authorize(getmethod('index'), PlacesType::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_places_types = operator_right('places_types', 'false', 'index');

        // Список типов помещений
        $places_types_query = PlacesType::moderatorLimit($answer_places_types)
        ->companiesLimit($answer_places_types)
        ->authors($answer_places_types)
        ->systemItem($answer_places_types) // Фильтр по системным записям
        ->template($answer_places_types) // Выводим шаблоны в список
        ->get();

        $places_types = [];

        foreach ($place->places_types as $place_type){
            $places_types[] = $place_type->id;
        }

        // Имя столбца
        $column = 'places_types_id';
        $request[$column] = $places_types;

        $filter['status'] = null;

        // dd($request);
        

        $places_types_checkboxer = addFilter($filter, $places_types_query, $request, 'Тип помещения', 'places_types', 'id', 'places_types', 'internal-self-one');

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
        $place = Place::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $place);

        // Удаляем пользователя с обновлением
        $place = Place::moderatorLimit($answer)->where('id', $id)->delete();

        if($place) {return redirect('/admin/places');} else {abort(403,'Что-то пошло не так!');};
    }
}
