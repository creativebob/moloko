<?php

namespace App\Http\Controllers;

// Модели
use App\ProcessesGroup;
use App\Entity;

// Валидация
use Illuminate\Http\Request;
use App\Http\Requests\ProcessesGroupRequest;

class ProcessesGroupController extends Controller
{
    // Настройки сконтроллера
    public function __construct(ProcessesGroup $processes_group)
    {
        $this->middleware('auth');
        $this->processes_group = $processes_group;
        $this->class = ProcessesGroup::class;
        $this->model = 'App\ProcessesGroup';
        $this->entity_alias = with(new $this->class)->getTable();
        $this->entity_dependence = false;
    }

    public function index(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // -----------------------------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -----------------------------------------------------------------------------------------------------------------------------

        $articles_groups = ArticlesGroup::with(
            'author',
            'company',
            'articles'
        )
        ->withCount('articles')
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        // ->booklistFilter($request)
        // ->filter($request, 'author_id')
        // ->filter($request, 'company_id')
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->paginate(30);


        return view('articles_groups.index',[
            'articles_groups' => $articles_groups,
            'page_info' => pageInfo($this->entity_alias),
            // 'filter' => $filter,
            'nested' => 'articles_count'
        ]);
    }

    public function create()
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        return view('articles_groups.create', [
            'articles_group' => new $this->class,
            'page_info' => pageInfo($this->entity_alias),
        ]);
    }

    public function store(ArticlesGroupRequest $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Наполняем сущность данными
        $articles_group = new ArticlesGroup;
        $articles_group->name = $request->name;
        $articles_group->description = $request->description;
        $articles_group->unit_id = $request->unit_id;

        $articles_group->set_status = $request->has('set_status');


        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // Если нет прав на создание полноценной записи - запись отправляем на модерацию
        if ($answer['automoderate'] == false){
            $articles_group->moderation = 1;
        }

        $articles_group->system_item = $request->system_item;
        $articles_group->display = $request->display;

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        $articles_group->company_id = $user->company_id;
        $articles_group->author_id = hideGod($user);

        $articles_group->save();

        return redirect()->route('articles_groups.index');
    }

    public function show(ArticlesGroup $articlesGroup)
    {
        //
    }

    public function edit(Request $request, $id)
    {

        $articles_group = ArticlesGroup::moderatorLimit(operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__)))
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $articles_group);

        $articles_group->load('unit');

        return view('articles_groups.edit', [
            'articles_group' => $articles_group,
            'page_info' => pageInfo($this->entity_alias),
        ]);
    }

    public function update(ArticlesGroupRequest $request, $id)
    {

        $articles_group = ArticlesGroup::moderatorLimit(operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__)))
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $articles_group);

        $articles_group->name = $request->name;
        $articles_group->description = $request->description;
        $articles_group->unit_id = $request->unit_id;

        if ($articles_group->articles->count() == 0) {
            $articles_group->set_status = $request->has('set_status');
        }


        // Модерация и системная запись
        $articles_group->system_item = $request->system_item;
        $articles_group->display = $request->display;

        $articles_group->moderation = $request->moderation;

        $articles_group->editor_id = hideGod($request->user());
        $articles_group->save();

        if ($articles_group) {
            return redirect()->route('articles_groups.index');
        } else {
            abort(403, 'Ошибка обновления группы артикулов');
        }
    }

    public function destroy(Request $request, $id)
    {

        $articles_group = ArticlesGroup::with('articles')
        ->moderatorLimit(operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__)))
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $articles_group);

        $articles_group->editor_id = hideGod($request->user());
        $articles_group->save();

        $articles_group->delete();

        if ($articles_group) {
            return redirect()->route('articles_groups.index');
        } else {
            abort(403, 'Ошибка удаления группы артикулов');
        }
    }


    public function ajax_count(Request $request)
    {

        $entity = Entity::whereAlias($request->entity)
        ->first(['model']);
        // dd($entity);
        $model = 'App\\'.$entity->model;

        $category = $model::withCount('groups')
        ->with(['groups'])
        ->findOrFail($request->category_id);
        // dd($category);

        $processes_groups = $category->groups;
        // dd($processes_groups);

        return view('processes.create.mode_select', compact('processes_groups'));


        // if ($category->groups_count > 0) {

        //     $articles_groups = $category->groups;
        //     return view('goods.create_modes.mode_select', compact('articles_groups'));

        // } else {

        //     return view('goods.create_modes.mode_add');
        // }

    }

    public function ajax_set_status(Request $request)
    {
        $category_id = $request->category_id;
        // $mode = 'mode-add';
        // $entity = 'service_categories';

        $entity = Entity::whereAlias($request->entity)
        ->first(['model']);
        // dd($entity);
        $model = 'App\\'.$entity->model;

        $category = $model::with(['groups' => function ($q) {
            $q->with('unit');
        }])
        ->findOrFail($category_id);

        $processes_groups = $category->groups;
        return view('goods.create_modes.mode_select', compact('processes_groups'));


    }

    public function ajax_processes_groups_list(Request $request)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, 'index');

        $relation = $request->category_entity;
        $category_id = $request->category_id;
        $processes_group_id = $request->processes_group_id;
        // dd($relation);

        // Главный запрос
        $processes_groups = ProcessesGroup::moderatorLimit($answer)
        ->systemItem($answer)
        ->companiesLimit($answer)
        ->whereHas($relation, function ($q) use ($relation, $category_id) {
            $q->where($relation.'.id', $category_id);
        })
        ->orWhere('id', $processes_group_id)
        ->orderBy('sort', 'asc')
        ->toBase()
        ->get(['id','name']);
        // dd($processes_groups);

        return view('includes.selects.processes_groups_select', compact('processes_groups', 'processes_group_id'));
    }
}
