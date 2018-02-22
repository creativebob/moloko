<?php

namespace App\Http\Controllers;

use App\User;
use App\Right;
use App\Page;
use App\Entity;

// Модели которые отвечают за работу с правами + политики
use App\RightsRole;
use App\Role;
use App\Policies\RightPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

// Запросы и их валидация
use Illuminate\Http\Request;
// use App\Http\Requests\UpdateUser;

// Прочие необходимые классы
use Illuminate\Support\Facades\Log;

class RightController extends Controller
{

    // Сущность над которой производит операции контроллер
    protected $entity_name = 'rights';
    protected $entity_dependence = false;

    public function index()
    {

        // Получаем метод
        $method = __FUNCTION__;

        // Подключение политики
        $this->authorize($method, Right::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, $method);
        // dd($answer['dependence']);

        // ---------------------------------------------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // ---------------------------------------------------------------------------------------------------------------------------------------------

        $rights = Right::with('actionentity')
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->filials($answer) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->orderBy('moderation', 'desc')
        ->paginate(30);

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        return view('rights.index', compact('rights', 'page_info'));
    }


    public function create()
    {

        // Пока этот функционал не работатет, так как и не нужен пока...
        $right = new Right;
        $entities_list = Entity::get()->pluck('entity_name', 'id');
        return view('rights.create', compact('right', 'entities_list'));
    }


    public function store(Request $request)
    {

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


    public function destroy($id)
    {
        //
    }
}
