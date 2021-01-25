<?php

namespace App\Http\Controllers;

use App\Album;
use App\PhotoSetting;
use Illuminate\Http\Request;
use App\Http\Requests\System\AlbumRequest;

class AlbumController extends Controller
{
    protected $entityAlias;
    protected $entityDependence;

    /**
     * AlbumController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->model = 'App\Album';
        $this->entityAlias = 'albums';
        $this->entityDependence = false;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        // Включение контроля активного фильтра
        $filter_url = autoFilter($request, $this->entityAlias);
        if (($filter_url != null) && ($request->filter != 'active')) {
            return Redirect($filter_url);
        };

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Album::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));
        // dd($answer);

        $albums = Album::with([
            'author',
            'company',
            'category'
        ])
            ->withCount('photos')

            // Старый метод показа с шаблонными
            ->whereHas('category', function ($query) {
                $query->whereNotNull('company_id')
                    ->where(function ($query) {
                        $query->orWhere('system', false)
                            ->orWhere('system', false);
                    })
                    ->orWhere('company_id', null)
                    ->where(function ($query) {
                        $query->where('system', false);
                    });
            })
            ->moderatorLimit($answer)
            ->companiesLimit($answer)
            ->systemItem($answer)
            ->booklistFilter($request)
//            ->filter($request, 'author')
//            ->filter($request, 'company')
            ->orderBy('moderation', 'desc')
            ->oldest('sort')
            ->paginate(30);

        // -----------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ------------------------------------------------------------------------------
        // -----------------------------------------------------------------------------------------------------------

        $filter = setFilter($this->entityAlias, $request, [
            'author',               // Автор записи
            'company',              // Компания
            'booklist'              // Списки пользователя
        ]);

        // Окончание фильтра -----------------------------------------------------------------------------------------

        // Инфо о странице
        $pageInfo = pageInfo($this->entityAlias);

        return view('system.pages.marketings.albums.index', compact('albums', 'pageInfo', 'filter'));
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
        $this->authorize(__FUNCTION__, Album::class);

        $album = Album::make();
        $album_settings = PhotoSetting::make();

        $pageInfo = pageInfo($this->entityAlias);

        return view('system.pages.marketings.albums.create', compact('album', 'album_settings', 'pageInfo'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param AlbumRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(AlbumRequest $request)
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Album::class);

        $data = $request->input();
        $album = Album::create($data);

        if ($album) {
            return redirect()->route('albums.index');
        } else {
            abort(403, __('errors.store'));
        }
    }

    // TODO - 22.01.21 - Хз работет ли
    public function show($id)
    {
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        $album = Album::moderatorLimit($answer)
            ->find($id);
        //        dd($album);
        if (empty($album)) {
            abort(403, __('errors.not_found'));
        }

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $album);

        $answer_photo = operator_right('photos', false, getmethod('index'));
        // dd($answer_photo);

        $album->load([
            'author',
            'photos' => function ($query) use ($answer_photo) {
                $query->moderatorLimit($answer_photo)
                    ->companiesLimit($answer_photo)
                    ->authors($answer_photo)
                    ->systemItem($answer_photo)
                    ->orderBy('sort', 'asc');
            }]);

        $pageInfo = pageInfo($this->entityAlias);

        return view('system.pages.marketings.albums.show', compact('album', 'pageInfo'));
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

        $album = Album::with([
            'photo_settings',
        ])
            ->moderatorLimit($answer)
            ->find($id);
        //        dd($album);
        if (empty($album)) {
            abort(403, __('errors.not_found'));
        }

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $album);

        // Инфо о странице
        $pageInfo = pageInfo($this->entityAlias);

        return view('system.pages.marketings.albums.edit', compact('album', 'pageInfo'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param AlbumRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(AlbumRequest $request, $id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $album = Album::with('photo_settings')
            ->moderatorLimit($answer)
            ->find($id);
//        dd($album);
        if (empty($album)) {
            abort(403, __('errors.not_found'));
        }

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $album);

        $data = $request->input();
        $album->update($data);

        if ($album) {
            return redirect()->route('albums.index');
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

        $album = Album::with([
            'photos'
        ])
            ->moderatorLimit($answer)
            ->find($id);
        //        dd($album);
        if (empty($album)) {
            abort(403, __('errors.not_found'));
        }
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $album);

        $album->delete();

        if ($album) {
            return redirect()->route('albums.index');
        } else {
            abort(403, __('errors.destroy'));
        }
    }

    public function sections($alias)
    {
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        $album = Album::moderatorLimit($answer)
            ->whereAlias($alias)
            ->first();

        // Подключение политики
        $this->authorize(getmethod('show'), $album);

        $answer_photo = operator_right('photos', false, getmethod('index'));
        // dd($answer_photo);

        $album->load([
            'author',
            'photos' => function ($query) use ($answer_photo) {
                $query->moderatorLimit($answer_photo)
                    ->companiesLimit($answer_photo)
                    ->authors($answer_photo)
                    ->systemItem($answer_photo)
                    ->orderBy('sort', 'asc');
            }
        ]);

        // Инфо о странице
        $pageInfo = pageInfo($this->entityAlias);

        return view('system.pages.marketings.albums.sections', compact('album', 'pageInfo'));
    }

    // ------------------------------------------- Ajax ---------------------------------------------

    // Модалка прикрепления альбома
    public function ajax_add(Request $request)
    {
        return view('news.albums.modal_albums');
    }

    // Список албомов
    public function ajax_get_select(Request $request)
    {
        return view('news.albums.select_albums', ['albums_category_id' => $request->albums_category_id]);
    }

    // Список получаем альбом
    public function ajax_get(Request $request)
    {

        $answer = operator_right($this->entityAlias, $this->entityDependence, 'index');

        $album = Album::moderatorLimit($answer)
            ->find($request->album_id);

        return view('news.albums.album', compact('album'));
    }

}
