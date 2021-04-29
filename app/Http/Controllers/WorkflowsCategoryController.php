<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\Photable;
use App\Http\Requests\System\WorkflowsCategoryUpdateRequest;
use App\Http\Requests\System\WorkflowsCategoryStoreRequest;
use App\WorkflowsCategory;
use Illuminate\Http\Request;

class WorkflowsCategoryController extends Controller
{
    protected $entityAlias;
    protected $entityDependence;

    /**
     * WorkflowsCategoryController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->model = 'App\WorkflowsCategory';
        $this->entityAlias = 'workflows_categories';
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
        $this->authorize(getmethod(__FUNCTION__), WorkflowsCategory::class);

        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        $workflowsCategories = WorkflowsCategory::with([
            'workflows',
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
        ->oldest('sort')
        ->get();

        // Отдаем Ajax
        if ($request->ajax()) {

            return view('system.common.categories.index.categories_list',
                [
                    'items' => $workflowsCategories,
                    'entity' => $this->entityAlias,
                    'class' => $this->model,
                    'type' => $this->type,
                    'count' => $workflowsCategories->count(),
                    'id' => $request->id,
                    'nested' => 'childs_count',
                    // 'nested' => 'workflows_products_count',
                ]
            );
        }

        // Отдаем на шаблон
        return view('system.common.categories.index.index',
            [
                'items' => $workflowsCategories,
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
        $this->authorize(getmethod(__FUNCTION__), WorkflowsCategory::class);

        return view('system.common.categories.create.modal.create', [
            'item' => WorkflowsCategory::make(),
            'entity' => $this->entityAlias,
            'title' => 'Добавление категории рабочих процессов',
            'parent_id' => $request->parent_id,
            'category_id' => $request->category_id,
            'pageInfo' => pageInfo($this->entityAlias),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param WorkflowsCategoryStoreRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(WorkflowsCategoryStoreRequest $request)
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), WorkflowsCategory::class);

        $data = $request->input();
        $workflowsCategory = WorkflowsCategory::create($data);

        if ($workflowsCategory) {
            return redirect()->route('workflows_categories.index', ['id' => $workflowsCategory->id]);
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
        $workflowsCategory = WorkflowsCategory::with([
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
//        dd($workflowsCategory);
        if (empty($workflowsCategory)) {
            abort(403, __('errors.not_found'));
        }

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $workflowsCategory);
        // dd($workflowsCategory_metrics);

        // Инфо о странице
        $pageInfo = pageInfo($this->entityAlias);

        $settings = $this->getPhotoSettings($this->entityAlias);

        // При добавлении метрики отдаем ajax новый список свойст и метрик
        if ($request->ajax()) {
            return view('products.common.metrics.properties_list', [
                'category' => $workflowsCategory,
                'pageInfo' => $pageInfo,
            ]);
        }

        // dd($goods_category->direction);
        return view('products.processes_categories.common.edit.edit', [
            'title' => 'Редактирование категории рабочих процессов',
            'category' => $workflowsCategory,
            'pageInfo' => $pageInfo,
            'settings' => $settings,
            'entity' => $this->entityAlias,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param WorkflowsCategoryUpdateRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(WorkflowsCategoryUpdateRequest $request, $id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        $workflowsCategory = WorkflowsCategory::moderatorLimit($answer)
        ->find($id);
        //        dd($workflowsCategory);
        if (empty($workflowsCategory)) {
            abort(403, __('errors.not_found'));
        }

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $workflowsCategory);

        $data = $request->input();
        $data['photo_id'] = $this->getPhotoId($workflowsCategory);
        $result = $workflowsCategory->update($data);

        if ($result) {

            $workflowsCategory->manufacturers()->sync($request->manufacturers);

            $metrics = session('access.all_rights.index-metrics-allow');
            if ($metrics) {
                $workflowsCategory->metrics()->sync($request->metrics);
            }

           // Переадресовываем на index
            return redirect()->route('workflows_categories.index', ['id' => $workflowsCategory->id]);
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
        $workflowsCategory = WorkflowsCategory::with([
            'childs',
            'workflows'
        ])
        ->moderatorLimit($answer)
        ->find($id);
        //        dd($workflowsCategory);
        if (empty($workflowsCategory)) {
            abort(403, __('errors.not_found'));
        }

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $workflowsCategory);
        $workflowsCategory->delete();

        if ($workflowsCategory) {
            // Переадресовываем на index
            return redirect()->route('workflows_categories.index', ['id' => $workflowsCategory->parent_id]);
        } else {
            $result = [
                'error_status' => 1,
                'error_message' => __('errors.destroy')
            ];
        }
    }
}
