<?php

namespace App\Http\Controllers;

use App\Http\Requests\RawsCategoryUpdateRequest;
use App\Http\Requests\RawsCategoryStoreRequest;
use App\RawsCategory;
use Illuminate\Http\Request;

class RawsCategoryController extends Controller
{

    // Настройки сконтроллера
    public function __construct(RawsCategory $raws_category)
    {
        $this->middleware('auth');
        $this->raws_category = $raws_category;
        $this->class = RawsCategory::class;
        $this->model = 'App\RawsCategory';
        $this->entity_alias = with(new $this->class)->getTable();
        $this->entity_dependence = false;
        $this->type = 'edit';
    }

    public function index(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $raws_categories = RawsCategory::with([
            'raws',
            'childs',
            'groups'
        ])
        ->withCount('childs')
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer)
        ->template($answer)
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->get();

        // Отдаем Ajax
        if ($request->ajax()) {

            return view('system.common.accordions.categories_list',
                [
                    'items' => $raws_categories,
                    'entity' => $this->entity_alias,
                    'class' => $this->model,
                    'type' => $this->type,
                    'count' => $raws_categories->count(),
                    'id' => $request->id,
                    // 'nested' => 'raws_products_count',
                ]
            );
        }

        // Отдаем на шаблон
        return view('system.common.accordions.index',
            [
                'items' => $raws_categories,
                'page_info' => pageInfo($this->entity_alias),
                'entity' => $this->entity_alias,
                'class' => $this->model,
                'type' => $this->type,
                'id' => $request->id,
                'nested' => 'childs_count',
                'filter' => setFilter($this->entity_alias, $request, [
                    'booklist',
                ]),
            ]
        );
    }

    public function create(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        return view('system.common.accordions.create', [
            'item' => new $this->class,
            'entity' => $this->entity_alias,
            'title' => 'Добавление категории сырья',
            'parent_id' => $request->parent_id,
            'category_id' => $request->category_id
        ]);
    }

    public function store(RawsCategoryStoreRequest $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        $data = $request->input();
        $raws_category = (new $this->class())->create($data);

        if ($raws_category) {
            // Переадресовываем на index
            return redirect()->route('raws_categories.index', ['id' => $raws_category->id]);
        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при записи категории сырья!',
            ];
        }
    }

    public function show($id)
    {
        //
    }

    public function edit(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $raws_category = RawsCategory::with([
            'manufacturers',
        ])
        ->moderatorLimit($answer)
        ->findOrFail($id);
        // dd($raws_category);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $raws_category);

        // Инфо о странице
        $page_info = pageInfo($this->entity_alias);

        $settings = getSettings($this->entity_alias);

        return view('products.articles_categories.common.edit.edit', [
            'title' => 'Редактирование категории сырья',
            'category' => $raws_category,
            'page_info' => $page_info,
            'settings' => $settings,
            'entity' => $this->entity_alias,
        ]);
    }

    public function update(RawsCategoryUpdateRequest $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $raws_category = RawsCategory::moderatorLimit($answer)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $raws_category);

        $data = $request->input();
        $result = $raws_category->update($data);

        if ($result) {

            // Производители
            $raws_category->manufacturers()->sync($request->manufacturers);

           // Переадресовываем на index
            return redirect()->route('raws_categories.index', ['id' => $raws_category->id]);
        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при обновлении категории сырья!'
            ];
        }
    }

    public function destroy(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $raws_category = RawsCategory::with([
            'childs',
            'raws'
        ])
        ->moderatorLimit($answer)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $raws_category);

        $parent_id = $raws_category->parent_id;

        $raws_category->delete();

        if ($raws_category) {

                // Переадресовываем на index
            return redirect()->route('raws_categories.index', ['id' => $parent_id]);
        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при удалении категории!'
            ];
        }
    }
}
