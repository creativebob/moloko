<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\Photable;
use App\Http\Requests\System\ImpactsCategoryStoreRequest;
use App\Http\Requests\System\ImpactsCategoryUpdateRequest;
use App\ImpactsCategory;
use Illuminate\Http\Request;

class ImpactsCategoryController extends Controller
{
    protected $entityAlias;
    protected $entityDependence;

    /**
     * ImpactsCategoryController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->model = 'App\ImpactsCategory';
        $this->entityAlias = 'impacts_categories';
        $this->entityDependence = false;
        $this->type = 'page';
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
        $this->authorize(getmethod(__FUNCTION__), ImpactsCategory::class);

        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        $impactsCategories = ImpactsCategory::with([
            'impacts',
            'childs',
            'groups'
        ])
            ->withCount('childs')
            ->moderatorLimit($answer)
            ->companiesLimit($answer)
            ->authors($answer)
            ->systemItem($answer)
            ->template($answer)
            // ->withCount('products')
            ->orderBy('moderation', 'desc')
            ->orderBy('sort', 'asc')
            ->get();

        // Отдаем Ajax
        if ($request->ajax()) {

            return view('system.common.categories.index.categories_list',
                [
                    'items' => $impactsCategories,
                    'entity' => $this->entityAlias,
                    'class' => $this->model,
                    'type' => $this->type,
                    'count' => $impactsCategories->count(),
                    'id' => $request->id,
                    'nested' => 'childs_count',
                    // 'nested' => 'impacts_products_count',
                ]
            );
        }

        // Отдаем на шаблон
        return view('system.common.categories.index.index',
            [
                'items' => $impactsCategories,
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
        $this->authorize(getmethod(__FUNCTION__), ImpactsCategory::class);

        return view('system.common.categories.create.modal.create', [
            'item' => ImpactsCategory::make(),
            'entity' => $this->entityAlias,
            'title' => 'Добавление категории обьектов воздействия',
            'parent_id' => $request->parent_id,
            'category_id' => $request->category_id,
            'pageInfo' => pageInfo($this->entityAlias),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ImpactsCategoryStoreRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(ImpactsCategoryStoreRequest $request)
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), ImpactsCategory::class);

        $data = $request->input();
        $impactsCategory = ImpactsCategory::create($data);

        if ($impactsCategory) {
            // Переадресовываем на index
            return redirect()->route('impacts_categories.index', ['id' => $impactsCategory->id]);
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
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit(Request $request, $id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $impactsCategory = ImpactsCategory::with([
            'manufacturers',
            'metrics' => function ($q) {
                $q->with([
                    'unit',
                    'values'
                ]);
            },
        ])
            ->moderatorLimit($answer)
            ->find($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $impactsCategory);
        // dd($impactsCategory_metrics);

        // Инфо о странице
        $pageInfo = pageInfo($this->entityAlias);

        $settings = $this->getPhotoSettings($this->entityAlias);

        // При добавлении метрики отдаем ajax новый список свойст и метрик
        if ($request->ajax()) {
            return view('products.common.metrics.properties_list', [
                'category' => $impactsCategory,
                'pageInfo' => $pageInfo,
            ]);
        }

        // dd($goods_category->direction);
        return view('products.articles_categories.common.edit.edit', [
            'title' => 'Редактирование категории обьектов воздействия',
            'category' => $impactsCategory,
            'pageInfo' => $pageInfo,
            'settings' => $settings,
            'entity' => $this->entityAlias,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ImpactsCategoryUpdateRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(ImpactsCategoryUpdateRequest $request, $id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        $impactsCategory = ImpactsCategory::moderatorLimit($answer)
            ->find($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $impactsCategory);

        $data = $request->input();
        $data['photo_id'] = $this->getPhotoId($impactsCategory);
        $result = $impactsCategory->update($data);

        if ($result) {

            $impactsCategory->manufacturers()->sync($request->manufacturers);

            $metrics = session('access.all_rights.index-metrics-allow');
            if ($metrics) {
                $impactsCategory->metrics()->sync($request->metrics);
            }

            // Переадресовываем на index
            return redirect()->route('impacts_categories.index', ['id' => $impactsCategory->id]);
        } else {
            $result = [
                'error_status' => 1,
                'error_message' => __('errors.update')
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
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $impactsCategory = ImpactsCategory::with([
            'childs',
            'impacts'
        ])
            ->moderatorLimit($answer)
            ->find($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $impactsCategory);

        $parent_id = $impactsCategory->parent_id;

        $impactsCategory->delete();

        if ($impactsCategory) {
            // Переадресовываем на index
            return redirect()->route('impacts_categories.index', ['id' => $parent_id]);
        } else {
            $result = [
                'error_status' => 1,
                'error_message' => __('errors.destroy')
            ];
        }
    }
}
