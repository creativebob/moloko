<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\Photable;
use App\Http\Requests\System\CategoryStoreRequest;
use App\Http\Requests\System\OutcomesCategoryUpdateRequest;
use App\OutcomesCategory;
use Illuminate\Http\Request;

class OutcomesCategoryController extends Controller
{

    /**
     * OutcomesCategoryController constructor.
     * @param OutcomesCategory $outcomes_category
     */
    public function __construct(OutcomesCategory $outcomes_category)
    {
        $this->middleware('auth');
        $this->outcomes_category = $outcomes_category;
        $this->class = OutcomesCategory::class;
        $this->model = 'App\OutcomesCategory';
        $this->entity_alias = with(new $this->class)->getTable();
        $this->entity_dependence = false;
        $this->type = 'page';
    }

    use Photable;

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $outcomes_categories = OutcomesCategory::with([
            'outcomes',
            'childs',
        ])
            ->withCount('childs')
            ->moderatorLimit($answer)
            ->companiesLimit($answer)
            ->authors($answer)
            ->systemItem($answer)
            ->template($answer)
            ->orderBy('moderation', 'desc')
            ->orderBy('sort')
            ->get();

        // Отдаем Ajax
        if ($request->ajax()) {

            return view('system.common.categories.index.categories_list',
                [
                    'items' => $outcomes_categories,
                    'entity' => $this->entity_alias,
                    'class' => $this->model,
                    'type' => $this->type,
                    'count' => $outcomes_categories->count(),
                    'id' => $request->id,
                    // 'nested' => 'raws_products_count',
                ]
            );
        }

        // Отдаем на шаблон
        return view('system.common.categories.index.index',
            [
                'items' => $outcomes_categories,
                'pageInfo' => pageInfo($this->entity_alias),
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

    /**
     * Show the form for creating a new resource.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create(Request $request)
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        return view('system.common.categories.create.modal.create', [
            'item' => OutcomesCategory::make(),
            'entity' => $this->entity_alias,
            'title' => 'Добавление категории выполненных работ',
            'parent_id' => $request->parent_id,
            'category_id' => $request->category_id
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CategoryStoreRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(CategoryStoreRequest $request)
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        $data = $request->validated();
        $outcomes_category = OutcomesCategory::create($data);

        if ($outcomes_category) {
            // Переадресовываем на index
            return redirect()->route('outcomes_categories.index', ['id' => $outcomes_category->id]);
        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при записи',
            ];
        }
    }

    /**
     * Display the specified resource.
     *
     * @param $id
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $outcomes_category = OutcomesCategory::moderatorLimit($answer)
            ->find($id);
//         dd($outcomes_category);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $outcomes_category);

        // Инфо о странице
        $pageInfo = pageInfo($this->entity_alias);

        $settings = getPhotoSettings($this->entity_alias);

        return view('system.common.categories.edit.page.edit', [
            'title' => 'Редактирование категории выполненных работ',
            'category' => $outcomes_category,
            'pageInfo' => $pageInfo,
            'settings' => $settings,
            'entity' => $this->entity_alias,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param OutcomesCategoryUpdateRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(OutcomesCategoryUpdateRequest $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $outcomes_category = OutcomesCategory::moderatorLimit($answer)
            ->find($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $outcomes_category);

        $data = $request->input();
        $data['photo_id'] = $this->getPhotoId($outcomes_category);
        $result = $outcomes_category->update($data);

        if ($result) {

            // Переадресовываем на index
            return redirect()->route('outcomes_categories.index', ['id' => $outcomes_category->id]);
        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при обновлении категории'
            ];
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
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $outcomes_category = OutcomesCategory::with([
            'childs',
            'outcomes'
        ])
            ->moderatorLimit($answer)
            ->find($id);

        if ($outcomes_category) {
            // Подключение политики
            $this->authorize(getmethod(__FUNCTION__), $outcomes_category);

            $parent_id = $outcomes_category->parent_id;

            $outcomes_category->delete();

            // Переадресовываем на index
            return redirect()->route('outcomes_categories.index', ['id' => $parent_id]);
        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при удалении категории'
            ];
        }
    }
}
