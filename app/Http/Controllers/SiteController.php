<?php

namespace App\Http\Controllers;

// Модели
use App\Site;
use App\Entity;

use App\Filters\SiteFilter;

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

    public function index(Request $request, SiteFilter $filters)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // -------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -------------------------------------------------------------------------------------------

        $sites = Site::with([
            'author',
            'company',
            'pages',
            'navigations'
        ])
        // ->withCount('pages')
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer)
        ->template($answer)
        ->booklistFilter($request)
//            ->filter($filters)
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
            'site' => Site::make(),
            'page_info' => pageInfo($this->entity_alias),
        ]);
    }


    public function store(SiteRequest $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        $data = $request->input();
        $site = Site::create($data);

        if ($site) {
            return redirect()
                ->route('sites.edit', [$site->id])
                ->with(['success' => 'Успешно сохранено']);
        } else {
            return back()
                ->withErrors(['msg' => 'Ошибка сохранения'])
                ->withInput();
        }

//        if ($site) {
//            return redirect()->route('sites.index');
//        } else {
//            abort(403, 'Ошибка записи сайта');
//        }
    }

    public function show(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod('update'));

        $site = Site::moderatorLimit($answer)
        ->findOrFail($id);
        // dd($site);

        // Подключение политики
        $this->authorize('update', $site);

        return view('sites.sections', [
            'site' => $site,
            'sections' => Entity::where('site', true)->get(),
            'page_info' => pageInfo($this->entity_alias)
        ]);
    }

    public function edit($id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $site = Site::moderatorLimit($answer)
        ->findOrFail($id);
        // dd($site);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $site);

        $site->load('plugins');

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

        $data = $request->input();
        $result = $site->update($data);

        if ($result) {
            return redirect()
                ->route('sites.edit', $site->id)
                ->with(['success' => 'Успешно сохранено']);
        } else {
            return back()
                ->withErrors(['msg' => 'Ошибка сохранения'])
                ->withInput();
        }

//        if ($result) {
//            return redirect()->route('sites.index');
//        } else {
//            abort(403, 'Ошибка обновления сайта');
//        }
    }

    public function destroy($id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $site = Site::with([
            'pages',
            'navigations',
        ])
        ->moderatorLimit($answer)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $site);

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

    public function kek(Request $request)
    {
        dd($request);
    }
}
