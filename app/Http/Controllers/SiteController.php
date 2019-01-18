<?php

namespace App\Http\Controllers;

// Модели
use App\Site;
use App\Entity;

// Валидация
use Illuminate\Http\Request;
use App\Http\Requests\SiteRequest;

class SiteController extends Controller
{

    // Настройки контроллера
    public function __construct(Site $site)
    {
        $this->middleware('auth');
        $this->site = $site;
        $this->class = Site::class;
        $this->model = 'App\Site';
        $this->entity_alias = with(new $this->class)->getTable();
        $this->entity_dependence = false;
    }

    public function index(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // -------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -------------------------------------------------------------------------------------------

        $sites = Site::with(
            'author',
            'company',
            'pages',
            'navigations'
        )
        // ->withCount('pages')
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer)
        ->booklistFilter($request)
        // ->filter($request, 'author_id')
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->paginate(30);


        // -----------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ------------------------------------------------------------------------------
        // -----------------------------------------------------------------------------------------------------------

        $filter = setFilter($this->entity_alias, $request, [
            // 'author',               // Автор записи
            // 'date_interval',     // Дата обращения
            'booklist'              // Списки пользователя
        ]);

        // Окончание фильтра -----------------------------------------------------------------------------------------

        return view('sites.index',[
            'sites' => $sites,
            'page_info' => pageInfo($this->entity_alias),
            'filter' => $filter,
            'nested' => 'pages_count'
        ]);
    }

    public function create(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        return view('sites.create', [
            'site' => new $this->class,
            'page_info' => pageInfo($this->entity_alias),
        ]);
    }


    public function store(SiteRequest $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Наполняем сущность данными
        $site = new Site;

        $site->name = $request->name;
        $site->domain = $request->domain;

        // Пока отсекаем по точке
        $site_alias = explode('.', $request->domain);
        $site->alias = $site_alias[0];

        $site->api_token = str_random(60);

        // Cистемная запись
        $site->system_item = $request->system_item;
        $site->display = $request->display;

        // Если нет прав на создание полноценной записи - запись отправляем на модерацию
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));
        if($answer['automoderate'] == false){
            $site->moderation = 1;
        }

        // Получаем данные для авторизованного пользователя
        $user = $request->user();
        $site->company_id = $user->company_id;
        $site->author_id = hideGod($user);

        $site->save();

        if ($site) {

            // // Пришем список пришедших разделов сайта
            // $site->menus()->attach($request->menus);

            // // Пришем список пришедших филиалов сайта
            // $site->departments()->attach($request->departments);

            return redirect()->route('sites.index');
        } else {
            abort(403, 'Ошибка записи сайта');
        }
    }

    public function show(Request $request)
    {
        //
    }

    public function edit(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $site = Site::moderatorLimit($answer)
        ->findOrFail($id);
        // dd($site);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $site);

        return view('sites.edit', [
            'site' => $site,
            'page_info' => pageInfo($this->entity_alias),
        ]);
    }

    public function update(SiteRequest $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $site = Site::moderatorLimit($answer)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $site);

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Скрываем бога
        $user_id = hideGod($user);

        $site->name = $request->name;

        if ($site->domain != $request->domain) {
            $site_alias = explode('.', $request->domain);
            $site->alias = $site_alias[0];

            $site->domain = $request->domain;
        }

        // Модерация и системная запись
        $site->system_item = $request->system_item;
        $site->display = $request->display;

        $site->moderation = $request->moderation;

        $site->editor_id = $user_id;
        $site->save();

        if ($site) {

            // Смотрим пришедние для сайта разделы и синхронизируем с существующими
            // if (isset($request->menus)) {
            //     $site->menus()->sync($request->menus);
            // } else {
            //     $site->menus()->detach();
            // }

            // Смотрим пришедние для сайта филиалы и синхронизируем с существующими
            // if (isset($request->menus)) {
            //     $site->departments()->sync($request->departments);
            // } else {
            //     $site->departments()->detach();
            // }

            return redirect()->route('sites.index');
        } else {
            abort(403, 'Ошибка обновления сайта');
        }
    }

    public function destroy(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $site = Site::moderatorLimit($answer)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $site);

        $site->editor_id = hideGod($request->user());
        $site->save();

        $site->delete();

        if ($site) {
            return redirect()->route('sites.index');
        } else {
            abort(403, 'Ошибка при удалении сайта');
        }
    }

    public function sections(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod('update'));

        $site = Site::moderatorLimit($answer)
        ->findOrFail($id);
        // dd($site);

        // Подключение политики
        $this->authorize('update', $site);

        $sections = Entity::where('site', true)->get();

        // Инфо о странице
        $page_info = pageInfo($this->entity_alias);

        return view('sites.sections', compact('site', 'sections', 'page_info'));
    }
}
