<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\Photable;
use App\Http\Requests\System\ServicesCategoryUpdateRequest;
use App\Http\Requests\System\ServicesCategoryStoreRequest;
use App\ServicesCategory;
use Illuminate\Http\Request;

class ServicesCategoryController extends Controller
{
    protected $entityAlias;
    protected $entityDependence;

    /**
     * ServicesCategoryController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->model = 'App\ServicesCategory';
        $this->entityAlias = 'services_categories';
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
        $this->authorize(getmethod(__FUNCTION__), ServicesCategory::class);

        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        $servicesCategories = ServicesCategory::with([
            'services',
            'childs',
            'groups',
            // 'direction'
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
        // dd($servicesCategories);

        // Отдаем Ajax
        if ($request->ajax()) {
            return view('system.common.categories.index.categories_list',
                [
                    'items' => $servicesCategories,
                    'entity' => $this->entityAlias,
                    'class' => $this->model,
                    'type' => $this->type,
                    'count' => $servicesCategories->count(),
                    'id' => $request->id,
                    'nested' => 'childs_count',
                ]
            );
        }

        // Отдаем на шаблон
        return view('system.common.categories.index.index',
            [
                'items' => $servicesCategories,
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
        $this->authorize(getmethod(__FUNCTION__), ServicesCategory::class);

        return view('system.common.categories.create.modal.create', [
            'item' => ServicesCategory::make(),
            'entity' => $this->entityAlias,
            'title' => 'Добавление категории услуг',
            'parent_id' => $request->parent_id,
            'category_id' => $request->category_id,
            'pageInfo' => pageInfo($this->entityAlias),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ServicesCategoryStoreRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(ServicesCategoryStoreRequest $request)
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), ServicesCategory::class);

        $data = $request->input();
        $servicesCategory = ServicesCategory::create($data);

        if ($servicesCategory) {
            // Переадресовываем на index
            return redirect()->route('services_categories.index', ['id' => $servicesCategory->id]);
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
        $servicesCategory = ServicesCategory::with([
            'workflows' => function ($q) {
                $q->with([
                    'category',
                    'process.group.unit'
                ]);
            },
            'manufacturers',
            'metrics' => function ($q) {
                $q->with([
                    'unit',
                    'values'
                ]);
            },
            'direction',
        ])
        ->moderatorLimit($answer)
        ->find($id);
//         dd(isset($servicesCategory->direction));

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $servicesCategory);

        // Инфо о странице
        $pageInfo = pageInfo($this->entityAlias);

        $settings = getPhotoSettings($this->entityAlias);

        // При добавлении метрики отдаем ajax новый список свойст и метрик
        if ($request->ajax()) {
            return view('products.common.metrics.properties_list', [
                'category' => $servicesCategory,
                'pageInfo' => $pageInfo,
            ]);
        }

        return view('products.processes_categories.common.edit.edit', [
            'title' => 'Редактирование категории услуг',
            'category' => $servicesCategory,
            'pageInfo' => $pageInfo,
            'settings' => $settings,
            'entity' => $this->entityAlias,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ServicesCategoryUpdateRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(ServicesCategoryUpdateRequest $request, $id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        $servicesCategory = ServicesCategory::moderatorLimit($answer)
        ->find($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $servicesCategory);

        $data = $request->input();
        $data['photo_id'] = $this->getPhotoId($servicesCategory);
        $result = $servicesCategory->update($data);

        if ($result) {

            $servicesCategory->manufacturers()->sync($request->manufacturers);

            $metrics = session('access.all_rights.index-metrics-allow');
            if ($metrics) {
                $servicesCategory->metrics()->sync($request->metrics);
            }

            $workflows = session('access.all_rights.index-workflows-allow');
            if ($workflows) {
                $servicesCategory->workflows()->sync($request->workflows);
            }

            // Переадресовываем на index
            return redirect()->route('services_categories.index', ['id' => $servicesCategory->id]);
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
        $servicesCategory = ServicesCategory::with([
            'childs',
            'services'
        ])
        ->moderatorLimit($answer)
        ->find($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $servicesCategory);

        $parent_id = $servicesCategory->parent_id;

        $servicesCategory->delete();

        if ($servicesCategory) {
            // Переадресовываем на index
            return redirect()->route('services_categories.index', ['id' => $parent_id]);
        } else {
            $result = [
                'error_status' => 1,
                'error_message' => __('errors.destroy')
            ];
        }
    }
}
