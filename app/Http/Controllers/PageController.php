<?php

namespace App\Http\Controllers;

// Подключаем модели
use App\Http\Controllers\System\Traits\Seoable;
use App\Http\Controllers\Traits\Photable;
use App\Page;
use App\Process;
use App\Site;

use App\PhotoSetting;

// Валидация
use Illuminate\Http\Request;
use App\Http\Requests\System\PageRequest;

// Транслитерация
use Illuminate\Support\Str;


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

    use Photable,
        Seoable;

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

        $site = Site::find($site_id);

        return view('pages.index', [
            'pages' => $pages,
            'pageInfo' => pageInfo($this->entity_alias),
            'site_id' => $site_id,
            'site' => $site
        ]);
    }

    public function create(Request $request, $site_id)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        $site = Site::find($site_id);

        return view('pages.create', [
            'page' => Page::make(),
            'pageInfo' => pageInfo($this->entity_alias),
            'site_id' => $site_id,
            'site' => $site
        ]);
    }

    public function store(PageRequest $request, $site_id)
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        $data = $request->input();
        $data['site_id'] = $site_id;
        $page = Page::create($data);


        $page->alias = empty($request->alias) ? Str::slug($request->title) : $request->alias;

        // Cохраняем / обновляем фото
        $photo_id = $this->getPhotoId($page);
        $page->photo_id = $photo_id;
        $page->save();

        if ($page) {

            // Переадресовываем на index
            return redirect()->route('pages.index', $site_id);
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
        $page = Page::moderatorLimit($answer)->where('site_id', $site_id)->find($id);
        // dd($page);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $page);

        $page->load([
            'seo.childs.params',
        ]);

        $site = Site::find($site_id);


        return view('pages.edit', [
            'page' => $page,
            'pageInfo' => pageInfo($this->entity_alias),
            'site_id' => $site_id,
            'site' => $site
        ]);
    }


    public function update(PageRequest $request, $site_id, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $page = Page::moderatorLimit($answer)->find($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $page);

        $data = $request->input();
        $page->update($data);


        if ($page) {

            $this->updateSeo($page);

            // Переадресовываем на index
            return redirect()->route('pages.index', $site_id);
        } else {
            abort(403, 'Ошибка при записи страницы!');
        }
    }

    public function destroy(Request $request, $site_id, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $page = Page::moderatorLimit($answer)->find($id);

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
    public function ajax_check (Request $request, $site_id)
    {

        // Проверка навигации по сайту в нашей базе данных
        $alias = $request->alias;

        $pages_count = Page::where([
            'site_id' => $site_id,
            'alias' => $alias
        ])
            ->count();

        return response()->json($pages_count);
    }

}
