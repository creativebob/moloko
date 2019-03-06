<?php

namespace App\Http\Controllers;

// Модели
use App\ArticlesGroup;

// Валидация
use Illuminate\Http\Request;
use App\Http\Requests\ArticlesGroupRequest;

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

        $articles_group->system_item = $request->system_item;
        $articles_group->display = $request->display;

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // Если нет прав на создание полноценной записи - запись отправляем на модерацию
        if ($answer['automoderate'] == false){
            $articles_group->moderation = 1;
        }

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
}
