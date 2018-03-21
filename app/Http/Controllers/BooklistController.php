<?php

namespace App\Http\Controllers;

use App\User;
use App\Booklist;
use App\List_item;

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

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Booklist::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ---------------------------------------------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // ---------------------------------------------------------------------------------------------------------------------------------------------


        // if($request->new_booklist){

        //     // ГЛАВНЫЙ ЗАПРОС:

        //     $booklist = Booklist::findOrFail($request->booklist_new_id);
        //     $booklist->booklist_name = $request->new_booklist;
        //     $booklist->author_id = Auth::user()->id;
        //     $booklist->entity_alias = $request->entity_alias;
        //     $booklist->save();

        //     $booklist_id[] = $booklist->id;

        //     $booklist = new Booklist;
        //     $booklist->booklist_name = 'Default';
        //     $booklist->author_id = Auth::user()->id;
        //     $booklist->entity_alias = $request->entity_alias;
        //     $booklist->save();

        // } else {

        //     $booklist_id = $request->booklist_id;
        // };


        $booklists = Booklist::moderatorLimit($answer)
        ->companiesLimit($answer)
        ->filials($answer) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->template($answer)
        ->orderBy('moderation', 'desc')
        ->paginate(30);

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        return view('booklists.index', compact('booklists', 'page_info'));
    }

    public function create()
    {
        // Инфо о странице
        $page_info = pageInfo($this->entity_name);
    }

    public function store(Request $request)
    {

        // Подключение политики
        // $this->authorize(getmethod(__FUNCTION__), Booklist::class);

        // // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        // $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // Получаем авторизованного пользователя
        $user = $request->user();

        // ГЛАВНЫЙ ЗАПРОС:
        $booklist = Booklist::where('author_id', $user->id)
        ->where('booklist_name', 'Default')
        ->where('entity_alias', $request->entity_alias)
        ->first();

        if($booklist){

            $booklist_id = $booklist->id;

        } else {

            $booklist = new Booklist;
            $booklist->booklist_name = 'Default';
            $booklist->author_id = $user->id;
            $booklist->entity_alias = $request->entity_alias;
            $booklist->save();

            $booklist_id = $booklist->id;

        };

        // ГЛАВНЫЙ ЗАПРОС:
        $list_item = List_item::where('booklist_id', $booklist_id)
        ->where('item_entity', $request->item_entity)
        ->first();

        if($list_item){

            $list_item_id = $list_item->id;
            $list_item = List_item::destroy($list_item_id);
        } else {

            $list_item = new List_item;
            $list_item->item_entity = $request->item_entity;
            $list_item->booklist_id = $booklist_id;
            $list_item->author_id = $user->id;
            $list_item->save();
        };



    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        // Инфо о странице
        $page_info = pageInfo($this->entity_name);
    }

    public function update(Request $request, $id)
    {

    }

    public function destroy($id)
    {
        //
    }

    public function setbooklist(Request $request)
    {

        if($request->new_booklist_name){

            $booklist = Booklist::where('author_id', $request->user()->id)
            ->where('booklist_name', 'Default')
            ->where('entity_alias', $request->entity_alias)
            ->first();

            if($booklist){

                $booklist->booklist_name = $request->new_booklist_name;
                $booklist->save();

                $booklist_id = $booklist->id;


                $booklist = new Booklist;
                $booklist->booklist_name = 'Default';
                $booklist->author_id = $request->user()->id;
                $booklist->entity_alias = $request->entity_alias;
                $booklist->save(); 

                $value = []; 
                $filter_query = null;

                $value = addFilter($value, $filter_query, $request, 'Мои списки:', 'booklist', 'booklist_id', $request->entity_alias);
                // echo json_encode($value, JSON_UNESCAPED_UNICODE);

                $name = 'booklist';

                return view('includes.inputs.booklister', ['name'=>$name, 'value'=>$value]);

            } else {

                 echo "Не нашли дефолтный";
            };


        } else {
            echo "Нихуя не пришло!";
        };

    }



}
