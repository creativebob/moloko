<?php

namespace App\Http\Controllers;

// Модели
use App\Place;
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
        ->booklistFilter($request)
        ->orderBy('moderation', 'desc')
        ->paginate(30);



        $filter_query = Place::moderatorLimit($answer)
        ->get();

        $filter['status'] = null;

        // $filter = addFilter($filter, $filter_query, $request, 'Выберите город:', 'city', 'city_id', 'location');
        // $filter = addFilter($filter, $filter_query, $request, 'Выберите сектор:', 'sector', 'sector_id');

        // Добавляем данные по спискам (Требуется на каждом контроллере)
        $filter = addBooklist($filter, $filter_query, $request, $this->entity_name);

        // dd($filter);
        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        // dd($filter);

        return view('places.index', compact('places', 'page_info', 'filter', 'user'));

    }

    public function create()
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

        // Список типов помещений
        $places_types = PlacesType::moderatorLimit($answer_places_types)
        ->companiesLimit($answer_places_types)
        ->authors($answer_places_types)
        ->systemItem($answer_places_types) // Фильтр по системным записям
        ->template($answer_places_types) // Выводим шаблоны в список
        ->get();

        return view('places.create', compact('place', 'page_info'));
    }


    public function store(Request $request)
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Place::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // Получаем авторизованного пользователя
        $user = $request->user();

        $place = new Place;
        $place->name = $request->name;
        $place->description = $request->description;
        $place->square = $request->square;

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

        if($place){} else {abort(403);};
        return redirect('places');
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

        return redirect('roles');

    }


    public function edit(Request $request, $id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $place = Place::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $place);

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        return view('places.edit', compact('place', 'page_info'));
    }


    public function update(PlaceRequest $request, $id)
    {


        // Получаем авторизованного пользователя
        $user = $request->user();

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $place = Place::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize('update', $place);

        $place->name = $request->name;
        $place->description = $request->description;

        $place->save();

        return redirect('places');

    }


    public function destroy($id)
    {
        //
    }
}
