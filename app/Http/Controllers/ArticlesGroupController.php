<?php

namespace App\Http\Controllers;

// Модели
use App\ArticlesGroup;
use App\Entity;

// Валидация
use Illuminate\Http\Request;
use App\Http\Requests\System\ArticlesGroupRequest;

class ArticlesGroupController extends Controller
{
    // Настройки сконтроллера
    public function __construct(ArticlesGroup $articles_group)
    {
        $this->middleware('auth');
        $this->articles_group = $articles_group;
        $this->class = ArticlesGroup::class;
        $this->model = 'App\ArticlesGroup';
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


        return view('products.articles_groups.index',[
            'articles_groups' => $articles_groups,
            'pageInfo' => pageInfo($this->entity_alias),
            // 'filter' => $filter,
            'nested' => 'articles_count'
        ]);
    }

    public function create()
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        return view('products.articles_groups.create', [
            'articles_group' => new $this->class,
            'pageInfo' => pageInfo($this->entity_alias),
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
            $articles_group->moderation = true;
        }

        $articles_group->system = $request->system;
        $articles_group->display = $request->display;

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        $articles_group->company_id = $user->company_id;
        $articles_group->author_id = hideGod($user);

        $articles_group->save();

        return redirect()->route('products.articles_groups.index');
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

        return view('products.articles_groups.edit', [
            'articles_group' => $articles_group,
            'pageInfo' => pageInfo($this->entity_alias),
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
        $articles_group->system = $request->system;
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
            return redirect()->route('products.articles_groups.index');
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

        $groups = $category->groups;
        // dd($articles_groups);

        return view('products.common.create.create_modes.mode_select', compact('groups'));


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

        $articles_groups = $category->groups;
        return view('goods.create_modes.mode_select', compact('articles_groups'));


    }

    public function ajax_articles_groups_list(Request $request)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, 'index');

        $relation = $request->category_entity;
        $category_id = $request->category_id;
        $articles_group_id = $request->articles_group_id;
        // dd($relation);

        // Главный запрос
        $articles_groups = ArticlesGroup::moderatorLimit($answer)
        ->systemItem($answer)
        ->companiesLimit($answer)
        ->whereHas($relation, function ($q) use ($relation, $category_id) {
            $q->where($relation.'.id', $category_id);
        })
        ->orWhere('id', $articles_group_id)
        ->orderBy('sort', 'asc')
        ->toBase()
        ->get(['id','name']);
        // dd($articles_groups);

        return view('includes.selects.articles_groups_select', compact('articles_groups', 'articles_group_id'));
    }
}
