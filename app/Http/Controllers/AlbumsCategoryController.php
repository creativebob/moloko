<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\Photable;
use App\Http\Requests\System\AlbumsCategoryUpdateRequest;
use App\Http\Requests\System\AlbumsCategoryStoreRequest;
use App\AlbumsCategory;
use Illuminate\Http\Request;

class AlbumsCategoryController extends Controller
{

    // Настройки сконтроллера
    public function __construct(AlbumsCategory $albums_category)
    {
        $this->middleware('auth');
        $this->albums_category = $albums_category;
        $this->entity_alias = with(new AlbumsCategory)->getTable();;
        $this->entity_dependence = false;
        $this->class = AlbumsCategory::class;
        $this->model = 'App\AlbumsCategory';
        $this->type = 'modal';
    }

    use Photable;

    public function index(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $albums_categories = AlbumsCategory::moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer)
        ->template($answer)
        ->withCount('albums')
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->get();

        // Отдаем Ajax
        if ($request->ajax()) {

            return view('system.common.categories.index.categories_list',
                [
                    'items' => $albums_categories,
                    'entity' => $this->entity_alias,
                    'class' => $this->model,
                    'type' => $this->type,
                    'count' => $albums_categories->count(),
                    'id' => $request->id,
                    // 'nested' => 'albums_count',
                ]
            );
        }

        // Отдаем на шаблон
        return view('system.common.categories.index.index',
            [
                'items' => $albums_categories,
                'pageInfo' => pageInfo($this->entity_alias),
                'entity' => $this->entity_alias,
                'class' => $this->model,
                'type' => $this->type,
                'id' => $request->id,
                // 'nested' => 'albums_count',
                'filter' => setFilter($this->entity_alias, $request, [
                    'booklist'
                ]),
            ]
        );
    }

    public function create(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        return view('system.common.categories.create.modal.create', [
            'item' => new $this->class,
            'entity' => $this->entity_alias,
            'title' => 'Добавление категории альбомов',
            'parent_id' => $request->parent_id,
            'category_id' => $request->category_id
        ]);
    }

    public function store(AlbumsCategoryStoreRequest $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        $data = $request->input();
        $albums_category = (new $this->class())->create($data);

        if ($albums_category) {

            // Переадресовываем на index
            return redirect()->route('albums_categories.index', ['id' => $albums_category->id]);
        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при записи категории альбомов!'
            ];
        }
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {

        $albums_category = AlbumsCategory::moderatorLimit(operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__)))->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $albums_category);

        return view('system.common.categories.edit', [
            'item' => $albums_category,
            'entity' => $this->entity_alias,
            'title' => 'Редактирование категории альбомов',
            'parent_id' => $albums_category->parent_id,
            'category_id' => $albums_category->category_id
        ]);
    }

    public function update(AlbumsCategoryUpdateRequest $request, $id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $albums_category = AlbumsCategory::moderatorLimit($answer)
            ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $albums_category);

        $data = $request->input();
        $data['photo_id'] = $this->getPhotoId($request, $albums_category);
        $result = $albums_category->update($data);

        if ($result) {

            // Переадресовываем на index
            return redirect()->route('albums_categories.index', ['id' => $albums_category->id]);
        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при записи категории альбомов!'
            ];
        }
    }

    public function destroy(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находится в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $albums_category = $albums_categories_count = AlbumsCategory::with([
            'childs',
            'albums'
        ])
            ->moderatorLimit($answer)
            ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $albums_category);

        $parent_id = $albums_category->parent_id;

        $albums_category->delete();

        if ($albums_category) {
            // Переадресовываем на index
            return redirect()->route('albums_categories.index', ['id' => $parent_id]);
        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при удалении категории альбомов!'
            ];
        }
    }
}
