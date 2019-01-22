<?php

namespace App\Http\Controllers;

// Подключаем модели
use App\Page;
use App\Site;

use App\PhotoSetting;

// Валидация
use Illuminate\Http\Request;
use App\Http\Requests\PageRequest;

// Транслитерация
use Transliterate;


class PageController extends Controller
{

    // Настройки контроллера
    public function __construct(Page $page)
    {
        $this->middleware('auth');
        $this->page = $page;
        $this->class = Page::class;
        $this->model = 'App\Page';
        $this->entity_alias = with(new $this->class)->getTable();
        $this->entity_dependence = false;
    }

    public function index(Request $request, $site_id)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence,  getmethod(__FUNCTION__));

        $pages = Page::with('author')
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer)
        ->where('site_id', $site_id)
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->paginate(30);
        // dd($navigations);

        return view('pages.index', [
            'pages' => $pages,
            'page_info' => pageInfo($this->entity_alias),
            'site_id' => $site_id
        ]);
    }

    public function create(Request $request, $site_id)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        return view('pages.create', [
            'page' => new $this->class,
            'page_info' => pageInfo($this->entity_alias),
            'site_id' => $site_id
        ]);
    }

    public function store(PageRequest $request, $site_id)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        $page = new Page;

        $page->name = $request->name;
        $page->title = $request->title;

        $page->alias = empty($request->alias) ? Transliterate::make($request->title, ['type' => 'url', 'lowercase' => true]) : $request->alias;

        $page->description = $request->description;
        $page->content = $request->content;

        $page->site_id = $site_id;

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // Если нет прав на создание полноценной записи - запись отправляем на модерацию
        if ($answer['automoderate'] == false){
            $page->moderation = 1;
        }

        // Системная запись
        $page->system_item = $request->system_item;
        $page->display = $request->display;

        // Получаем данные для авторизованного пользователя
        $user = $request->user();
        $page->company_id = $user->company_id;
        $page->author_id = hideGod($user);

        $page->save();

        // Cохраняем / обновляем фото
        savePhoto($request, $page);

        if ($page) {

            // Переадресовываем на index
            return redirect()->route('pages.index', ['site_id' => $site_id]);
        } else {
            abort(403, 'Ошибка при записи страницы!');
        }
    }

    public function show($id)
    {
        //
    }

    public function edit(Request $request, $site_id, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $page = Page::moderatorLimit($answer)->where('site_id', $site_id)->findOrFail($id);
        // dd($page);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $page);

        return view('pages.edit', [
            'page' => $page,
            'page_info' => pageInfo($this->entity_alias),
            'site_id' => $site_id
        ]);
    }


    public function update(PageRequest $request, $site_id, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $page = Page::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $page);

        $page->name = $request->name;
        $page->title = $request->title;
        $page->alias = $request->alias;

        $page->description = $request->description;
        $page->content = $request->content;

        // Если нет прав на создание полноценной записи - запись отправляем на модерацию
        if ($answer['automoderate'] == false){
            $page->moderation = 1;
        }

        // Системная запись
        $page->system_item = $request->system_item;
        $page->display = $request->display;

        $page->editor_id = hideGod($request->user());
        $page->save();

        // Cохраняем / обновляем фото
        savePhoto($request, $page);

        if ($page) {

            // Переадресовываем на index
            return redirect()->route('pages.index', ['site_id' => $site_id]);
        } else {
            abort(403, 'Ошибка при записи страницы!');
        }
    }

    public function destroy(Request $request, $site_id, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $page = Page::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $page);

        $page->editor_id = hideGod($request->user());
        $page->save();

        // Удаляем страницу с обновлением
        $page->delete();

        if ($page) {

            // Переадресовываем на index
            return redirect()->route('pages.index', ['site_id' => $site_id]);
        } else {
            abort(403, 'Ошибка при удалении страницы');
        }
    }

    // ------------------------------------------- Ajax ---------------------------------------------

    // Проверка наличия в базе
    public function ajax_check (Request $request, $alias)
    {

        // Проверка навигации по сайту в нашей базе данных
        $page_alias = $request->alias;
        $site = Site::withCount(['pages' => function($query) use ($page_alias) {
            $query->whereAlias($page_alias);
        }])->whereAlias($alias)->first();

        // Если такая навигация есть
        if ($site->pages_count > 0) {
            $result = [
                'error_status' => 1,
            ];

        // Если нет
        } else {
            $result = [
                'error_status' => 0,
            ];
        }

        return json_encode($result, JSON_UNESCAPED_UNICODE);
    }

}
