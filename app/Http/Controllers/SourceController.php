<?php

namespace App\Http\Controllers;

// Модели
use App\Source;
use App\User;

use Illuminate\Http\Request;

class SourceController extends Controller
{

    // Настройки сконтроллера
    public function __construct(Source $department)
    {
        $this->middleware('auth');
        $this->department = $department;
        $this->entity_alias = with(new Source)->getTable();;
        $this->entity_dependence = false;
        $this->class = Source::class;
        $this->model = 'App\Source';
        $this->type = 'menu';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        // $filter_url = autoFilter($request, $this->entity_name);
        // if(($filter_url != null)&&($request->filter != 'active')){return Redirect($filter_url);};

        // Подключение политики
        // $this->authorize(getmethod(__FUNCTION__), Company::class);

        // Получаем авторизованного пользователя
        $user = $request->user();

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ------------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // ------------------------------------------------------------------------------------------------------------

        $sources = Source::companiesLimit($answer)
        ->moderatorLimit($answer)
        ->authors($answer)
        ->systemItem($answer)
        ->booklistFilter($request)
        ->orderBy('sort', 'asc')
        ->paginate(30);


        // -----------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ------------------------------------------------------------------------------
        // -----------------------------------------------------------------------------------------------------------

        $filter = setFilter($this->entity_alias, $request, [
            // 'author',               // Автор записи
            // 'company',              // Компания
            'booklist'              // Списки пользователя
        ]);

        // Окончание фильтра -----------------------------------------------------------------------------------------

        return view('sources.index',[
            'sources' => $sources,
            'page_info' => pageInfo($this->entity_alias),
            'filter' => $filter
        ]);

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
