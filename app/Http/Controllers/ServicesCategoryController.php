<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\Photable;
use App\Http\Requests\ServicesCategoryUpdateRequest;
use App\Http\Requests\ServicesCategoryStoreRequest;
use App\ServicesCategory;
use Illuminate\Http\Request;

class ServicesCategoryController extends Controller
{

    /**
     * ServicesCategoryController constructor.
     * @param ServicesCategory $services_category
     */
    public function __construct(ServicesCategory $services_category)
    {
        $this->middleware('auth');
        $this->services_category = $services_category;
        $this->class = ServicesCategory::class;
        $this->model = 'App\ServicesCategory';
        $this->entity_alias = with(new $this->class)->getTable();
        $this->entity_dependence = false;
        $this->type = 'edit';
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

        $services_categories = ServicesCategory::with([
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
        // dd($services_categories);

        // Отдаем Ajax
        if ($request->ajax()) {
            return view('system.common.accordions.categories_list',
                [
                    'items' => $services_categories,
                    'entity' => $this->entity_alias,
                    'class' => $this->model,
                    'type' => $this->type,
                    'count' => $services_categories->count(),
                    'id' => $request->id,
                    'nested' => 'childs_count',
                ]
            );
        }

        // Отдаем на шаблон
        return view('system.common.accordions.index',
            [
                'items' => $services_categories,
                'page_info' => pageInfo($this->entity_alias),
                'entity' => $this->entity_alias,
                'class' => $this->model,
                'type' => $this->type,
                'id' => $request->id,
                'nested' => 'childs_count',
                'filter' => setFilter($this->entity_alias, $request, [
                    'booklist'
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

        return view('system.common.accordions.create', [
            'item' => new $this->class,
            'entity' => $this->entity_alias,
            'title' => 'Добавление категории услуг',
            'parent_id' => $request->parent_id,
            'category_id' => $request->category_id,
            'page_info' => pageInfo($this->entity_alias),
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
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        $data = $request->input();
        $services_category = ServicesCategory::create($data);

        if ($services_category) {
            // Переадресовываем на index
            return redirect()->route('services_categories.index', ['id' => $services_category->id]);
        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при записи категории услуг!',
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
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $services_category = ServicesCategory::with([
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
        ->findOrFail($id);
//         dd(isset($services_category->direction));

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $services_category);

        // Инфо о странице
        $page_info = pageInfo($this->entity_alias);

        $settings = getSettings($this->entity_alias);

        // При добавлении метрики отдаем ajax новый список свойст и метрик
        if ($request->ajax()) {
            return view('products.common.metrics.properties_list', [
                'category' => $services_category,
                'page_info' => $page_info,
            ]);
        }

        return view('products.processes_categories.common.edit.edit', [
            'title' => 'Редактирование категории услуг',
            'category' => $services_category,
            'page_info' => $page_info,
            'settings' => $settings,
            'entity' => $this->entity_alias,
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
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $services_category = ServicesCategory::moderatorLimit($answer)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $services_category);

        $data = $request->input();
        $data['photo_id'] = $this->getPhotoId($request, $services_category);
        $result = $services_category->update($data);

        if ($result) {

            $services_category->manufacturers()->sync($request->manufacturers);
            $metrics = session('access.all_rights.index-metrics-allow');
            if ($metrics) {
                $services_category->metrics()->sync($request->metrics);
            }

            $workflows = session('access.all_rights.index-workflows-allow');
            if ($workflows) {
                $services_category->workflows()->sync($request->workflows);
            }

            // Переадресовываем на index
            return redirect()->route('services_categories.index', ['id' => $services_category->id]);
        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при записи категории услуг!'
            ];
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $services_category = ServicesCategory::with([
            'childs',
            'services'
        ])
        ->moderatorLimit($answer)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $services_category);

        $parent_id = $services_category->parent_id;

        $services_category->delete();

        if ($services_category) {
            // Переадресовываем на index
            return redirect()->route('services_categories.index', ['id' => $parent_id]);
        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при удалении категории!'
            ];
        }
    }
}
