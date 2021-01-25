<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\Photable;
use App\News;
use App\RubricatorsItem;
use Illuminate\Http\Request;
use App\Http\Requests\System\NewsRequest;
use Illuminate\Support\Facades\Storage;

class NewsController extends Controller
{
    protected $entityAlias;
    protected $entityDependence;

    /**
     * NewsController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->entityAlias = 'news';
        $this->entityDependence = false;
    }

    use Photable;

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), News::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        $news = News::with([
            'author',
            'albums',
            'company.location.city'
        ])
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer)
        ->booklistFilter($request)  // Фильтр по спискам
//        ->dateIntervalFilter($request, 'publish_begin_date') // Интервальный фильтр по дате публикации
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->orderBy('publish_begin_date', 'desc')
        ->paginate(30);

        // -----------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ------------------------------------------------------------------------------
        // -----------------------------------------------------------------------------------------------------------

        $filter = setFilter($this->entityAlias, $request, [
            'date_interval',        // Дата
            'booklist'              // Списки пользователя
        ]);

        // Окончание фильтра -----------------------------------------------------------------------------------------

        // Инфо о странице
        $pageInfo = pageInfo($this->entityAlias);

        return view('system.pages.marketings.news.index', compact('news', 'pageInfo'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create()
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), News::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('rubricators_items', false, 'index');

        $rubricatorsItemsCount = RubricatorsItem::moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer)
        ->template($answer)
        ->count();

        if ($rubricatorsItemsCount == 0){

            // Описание ошибки
            $ajax_error = [];
            $ajax_error['title'] = "Обратите внимание!";
            $ajax_error['text'] = "Для начала необходимо создать рубрикатор и наполнить его рубриками. А уже потом будем добавлять новости. Ок?";
            $ajax_error['link'] = "/admin/rubricators";
            $ajax_error['title_link'] = "Идем в рубрикаторы";

            return view('ajax_error', compact('ajax_error'));
        }

        $curNews = News::make();

        // Инфо о странице
        $pageInfo = pageInfo($this->entityAlias);

        return view('system.pages.marketings.news.create', compact('curNews', 'pageInfo'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param NewsRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(NewsRequest $request)
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), News::class);

        $data = $request->input();
        $curNews = News::create($data);

        if ($curNews) {
            $photoId = $this->getPhotoId($curNews);
            $curNews->update([
                'photo_id' => $photoId
            ]);

            return redirect()->route('news.index');
        } else {
            abort(403, __('errors.store'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        $curNews = News::with([
            'albums.category',
        ])
        ->moderatorLimit($answer)
        ->find($id);
        //        dd($curNews);
        if (empty($curNews)) {
            abort(403, __('errors.not_found'));
        }

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $curNews);

        // Инфо о странице
        $pageInfo = pageInfo($this->entityAlias);

        return view('system.pages.marketings.news.edit', compact('curNews', 'pageInfo'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param NewsRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(NewsRequest $request, $id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        $curNews = News::moderatorLimit($answer)
        ->find($id);
        //        dd($curNews);
        if (empty($curNews)) {
            abort(403, __('errors.not_found'));
        }

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $curNews);

        $data = $request->input();
        $data['photo_id'] = $this->getPhotoId($curNews);
        $curNews->update($data);

        if ($curNews) {
            return redirect()->route('news.index');
        } else {
            abort(403, __('errors.update'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        $curNews = News::moderatorLimit($answer)
        ->find($id);
        //        dd($curNews);
        if (empty($curNews)) {
            abort(403, __('errors.not_found'));
        }

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $curNews);
        $curNews->delete();

        if ($curNews) {
            return redirect()->route('news.index');
        } else {
            abort(403, __('errors.destroy'));
        }
    }
}
