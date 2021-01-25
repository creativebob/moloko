<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\Photable;
use App\Http\Requests\System\AlbumsCategoryUpdateRequest;
use App\Http\Requests\System\AlbumsCategoryStoreRequest;
use App\AlbumsCategory;
use Illuminate\Http\Request;

class AlbumsCategoryController extends Controller
{
    protected $entityAlias;
    protected $entityDependence;

    /**
     * AlbumsCategoryController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->entityAlias = with(new AlbumsCategory)->getTable();;
        $this->entityDependence = false;
        $this->model = 'App\AlbumsCategory';
        $this->type = 'modal';
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
        $this->authorize(getmethod(__FUNCTION__), AlbumsCategory::class);

        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        $albumsCategories = AlbumsCategory::with([
            'albums',
            'childs',
        ])
            ->withCount('childs')
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer)
        ->template($answer)
        ->withCount('albums')
        ->orderBy('moderation', 'desc')
        ->orderBy('sort')
        ->get();

        // Отдаем Ajax
        if ($request->ajax()) {

            return view('system.common.categories.index.categories_list',
                [
                    'items' => $albumsCategories,
                    'entity' => $this->entityAlias,
                    'class' => $this->model,
                    'type' => $this->type,
                    'count' => $albumsCategories->count(),
                    'id' => $request->id,
                    // 'nested' => 'albums_count',
                ]
            );
        }

        // Отдаем на шаблон
        return view('system.common.categories.index.index',
            [
                'items' => $albumsCategories,
                'pageInfo' => pageInfo($this->entityAlias),
                'entity' => $this->entityAlias,
                'class' => $this->model,
                'type' => $this->type,
                'id' => $request->id,
                 'nested' => 'childs_count',
                'filter' => setFilter($this->entityAlias, $request, [
                    'booklist'
                ]),
            ]
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create(Request $request)
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), AlbumsCategory::class);

        return view('system.common.categories.create.modal.create', [
            'item' => AlbumsCategory::make(),
            'entity' => $this->entityAlias,
            'title' => 'Добавление категории альбомов',
            'parent_id' => $request->parent_id,
            'category_id' => $request->category_id
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param AlbumsCategoryStoreRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(AlbumsCategoryStoreRequest $request)
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), AlbumsCategory::class);

        $data = $request->input();
        $albumsCategory = AlbumsCategory::create($data);

        if ($albumsCategory) {
            return redirect()->route('albums_categories.index', ['id' => $albumsCategory->id]);
        } else {
            $result = [
                'error_status' => 1,
                'error_message' => __('errors.store'),
            ];
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

        $albumsCategory = AlbumsCategory::moderatorLimit($answer)
            ->find($id);
        //        dd($albumsCategory);
        if (empty($albumsCategory)) {
            abort(403, __('errors.not_found'));
        }

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $albumsCategory);

        return view('system.common.categories.edit.modal.edit', [
            'item' => $albumsCategory,
            'entity' => $this->entityAlias,
            'title' => 'Редактирование категории альбомов',
            'parent_id' => $albumsCategory->parent_id,
            'category_id' => $albumsCategory->category_id
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param AlbumsCategoryUpdateRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(AlbumsCategoryUpdateRequest $request, $id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        $albumsCategory = AlbumsCategory::moderatorLimit($answer)
            ->find($id);
        //        dd($albumsCategory);
        if (empty($albumsCategory)) {
            abort(403, __('errors.not_found'));
        }

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $albumsCategory);

        $data = $request->input();
        $data['photo_id'] = $this->getPhotoId($albumsCategory);
        $result = $albumsCategory->update($data);

        if ($result) {
            return redirect()->route('albums_categories.index', ['id' => $albumsCategory->id]);
        } else {
            $result = [
                'error_status' => 1,
                'error_message' => abort(403, __('errors.update'))
            ];
        }
    }

    public function destroy(Request $request, $id)
    {
        // Получаем из сессии необходимые данные (Функция находится в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        $albumsCategory = AlbumsCategory::with([
            'childs',
            'albums'
        ])
            ->moderatorLimit($answer)
            ->find($id);
        //        dd($albumsCategory);
        if (empty($albumsCategory)) {
            abort(403, __('errors.not_found'));
        }

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $albumsCategory);
        $albumsCategory->delete();

        if ($albumsCategory) {
            // Переадресовываем на index
            return redirect()->route('albums_categories.index', ['id' => $albumsCategory->parent_id]);
        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при удалении категории альбомов!'
            ];
        }
    }
}
