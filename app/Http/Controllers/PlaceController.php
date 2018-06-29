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

    public function index()
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
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
    public function destroy($id)
    {
        //
    }
}
