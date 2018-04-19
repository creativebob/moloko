<?php

namespace App\Http\Controllers;

use App\Album;
use App\User;
use App\List_item;
use App\Booklist;

use App\Http\Controllers\Session;

// Модели которые отвечают за работу с правами + политики
use App\Role;
use App\Policies\AlbumPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

// Запросы и их валидация
use Illuminate\Http\Request;
// use App\Http\Requests\AlbumRequest;

// Прочие необходимые классы
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AlbumController extends Controller
{

    // Сущность над которой производит операции контроллер
    protected $entity_name = 'albums';
    protected $entity_dependence = false;

    public function index(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), User::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));
        // dd($answer);

        // --------------------------------------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // --------------------------------------------------------------------------------------------------------------------------------------

        $albums = Album::with('author', 'company')
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->filials($answer) // $industry должна существовать только для зависимых от филиала, иначе $industry должна null
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->booklistFilter($request) 
        ->orderBy('sort', 'asc')
        ->paginate(30);

        // Запрос для фильтра
        $filter_query = Album::moderatorLimit($answer)
        ->companiesLimit($answer)
        ->filials($answer) // $industry должна существовать только для зависимых от филиала, иначе $industry должна null
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->orderBy('sort', 'asc')
        ->get();

        $filter['status'] = null;

        // Добавляем данные по спискам (Требуется на каждом контроллере)
        $filter = addFilter($filter, $filter_query, $request, 'Мои списки:', 'booklist', 'booklist_id', $this->entity_name);

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);
        $user = $request->user();

        return view('albums.index', compact('albums', 'page_info', 'filter', 'album', 'user'));
    }


    public function create(Request $request)
    {

        dd(public_path());

        $user = $request->user();

        // Подключение политики
        $this->authorize(__FUNCTION__, Album::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // Функция из Helper отдает массив со списками для SELECT
        $departments_list = getLS('users', 'view', 'departments');
        $filials_list = getLS('users', 'view', 'departments');

        $album = new Album;

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        return view('albums.create', compact('user', 'album', 'departments_list', 'roles_list', 'page_info'));
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
