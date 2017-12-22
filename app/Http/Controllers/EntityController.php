<?php

namespace App\Http\Controllers;

// Модели для текущей работы
use App\User;
use App\Entity;
use App\Page;

// Модели которые отвечают за работу с правами + политики
use App\RightsRole;
use App\Role;
use App\Policies\EntityPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

// Запросы и их валидация
use Illuminate\Http\Request;
// use App\Http\Requests\Updateentity;

// Прочие необходимые классы
use Illuminate\Support\Facades\Log;

class EntityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $model = new Entity;
        // $this->authorize('index', $model);

        $entities = Entity::paginate(30);
        $menu = Page::get();
        return view('entities.index', compact('entities', 'menu'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // $this->authorize('create', Entity::class);

        $entity = new Entity;
        $menu = Page::get();
        return view('entities.create', compact('entity', 'menu'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // $this->authorize('create', Entity::class);

        $user = Auth::user();  
        $entity = new entity;
        $entity->entity_name = $request->entity_name;
        $entity->entity_alias = $request->entity_alias;
 
        $entity->author_id = $user->id;
        $entity->save();
        return redirect('entities');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $entity = Entity::findOrFail($id);
        // $this->authorize('update', $entity);

        $menu = Page::get();
        return view('entities.show', compact('entity', 'menu'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $entity = Entity::findOrFail($id);
        // $this->authorize('update', $entity);

        $menu = Page::get();
        return view('entities.show', compact('entity', 'menu'));
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
        $entity = Entity::findOrFail($id);
        // $this->authorize('update', $entity);
        $entity->entity_name = $request->entity_name;
        $entity->entity_alias = $request->entity_alias;

        $entity->save();
        return redirect('entities');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $entity = Entity::findOrFail($id);
        // $this->authorize('update', $entity); 

        // Удаляем пользователя с обновлением
        $entity = Entity::destroy($id);
        if ($entity) {
          return Redirect('/entities');
        } else {
          echo 'Произошла ошибка';
        }; 

        Log::info('Удалили запись из таблица Сущности. ID: ' . $id);
    }
}
