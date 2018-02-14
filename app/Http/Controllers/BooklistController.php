<?php

namespace App\Http\Controllers;

use App\User;
use App\Booklist;
use App\Http\Controllers\Session;
use App\Scopes\ModerationScope;

// Модели которые отвечают за работу с правами + политики
use App\Policies\BooklistPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

// Запросы и их валидация
use Illuminate\Http\Request;
use App\Http\Requests\RequestBooklist;

// Прочие необходимые классы
use Illuminate\Support\Facades\Log;

class BooklistController extends Controller
{

    // Сущность над которой производит операции контроллер
    protected $entity_name = 'booklists';
    protected $entity_dependence = false;
    
    public function index(Request $request)
    {
        // Получаем метод
        $method = __FUNCTION__;

        // Подключение политики
        $this->authorize($method, Booklist::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->$entity_dependence, $method);
        // dd($answer['dependence']);

        // ---------------------------------------------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // ---------------------------------------------------------------------------------------------------------------------------------------------

        $booklists = Booklist::withoutGlobalScope($answer['moderator'])
        ->moderatorFilter($answer)
        ->companiesFilter($answer)
        ->filials($answer) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->orWhere('id', $request->user()->id) // Только для сущности USERS
        ->orderBy('moderated', 'desc')
        ->paginate(30);

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        return view('booklists.index', compact('booklists', 'page_info'));
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
