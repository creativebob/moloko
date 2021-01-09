<?php

namespace App\Http\Controllers;

// Модели
use App\CatalogsService;

// Валидация
use Illuminate\Http\Request;
use App\Http\Requests\System\CatalogsServiceRequest;

// Транслитерация
use Illuminate\Support\Str;

class CatalogsServiceController extends Controller
{
    protected $entityAlias;
    protected $entityDependence;

    /**
     * CatalogsServiceController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->entityAlias = 'catalogs_services';
        $this->entityDependence = false;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index()
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), CatalogsService::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        $catalogsServices = CatalogsService::with([
            'prices_services.service.process',
            'author',
        ])
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer)
        ->template($answer)
        ->orderBy('moderation', 'desc')
        ->oldest('sort')
        ->paginate(30);
        // dd($catalogsServices);

        // Инфо о странице
        $pageInfo = pageInfo($this->entityAlias);

        return view('system.pages.catalogs.services.catalogs_services.index', compact('catalogsServices', 'pageInfo'));
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
        $this->authorize(getmethod(__FUNCTION__), CatalogsService::class);

        $catalogServices = CatalogsService::make();

        $pageInfo = pageInfo($this->entityAlias);

        return view('system.pages.catalogs.services.catalogs_services.create', compact('catalogServices', 'pageInfo'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CatalogsServiceRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(CatalogsServiceRequest $request)
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), CatalogsService::class);

        $data = $request->input();
        $catalogServices = CatalogsService::create($data);

        if ($catalogServices) {

            $departments = session('access.all_rights.index-departments-allow');
            if ($departments) {
                $catalogServices->filials()->sync($request->filials);
            }

            return redirect()->route('catalogs_services.index');

        } else {
            abort(403, __('errors.store'));
        }
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
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        $catalogServices = CatalogsService::moderatorLimit($answer)
        ->find($id);
//        dd($catalogServices);
        if (empty($catalogServices)) {
            abort(403, __('errors.not_found'));
        }

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $catalogServices);

        $catalogServices->load([
            'filials',
            'agency_schemes'
        ]);

        $pageInfo = pageInfo($this->entityAlias);

        return view('system.pages.catalogs.services.catalogs_services.edit', compact('catalogServices', 'pageInfo'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param CatalogsServiceRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(CatalogsServiceRequest $request, $id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        $catalogServices = CatalogsService::moderatorLimit($answer)
        ->find($id);
        //        dd($catalogServices);
        if (empty($catalogServices)) {
            abort(403, __('errors.not_found'));
        }

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $catalogServices);

        $data = $request->input();
        $result = $catalogServices->update($data);

        if ($result) {
            $departments = session('access.all_rights.index-departments-allow');
            if ($departments) {
                $catalogServices->filials()->sync($request->filials);
            }

            return redirect()->route('catalogs_services.index');
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

        // ГЛАВНЫЙ ЗАПРОС:
        $catalogServices = CatalogsService::with([
            'items'
        ])
        ->moderatorLimit($answer)
        ->find($id);
        //        dd($catalogServices);
        if (empty($catalogServices)) {
            abort(403, __('errors.not_found'));
        }

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $catalogServices);

        $catalogServices->delete();

        if ($catalogServices) {
            return redirect()->route('catalogs_services.index');
        } else {
            abort(403, 'Ошибка при удалении каталога!');
        }
    }

    // ------------------------------------------------ Ajax -------------------------------------------------

    /**
     * Проверка наличия в базе
     *
     * @param Request $request
     * @param $alias
     * @return \Illuminate\Http\JsonResponse
     */
    public function ajax_check (Request $request, $alias)
    {

        // Получаем авторизованного пользователя
        $user = $request->user();

        // Проверка каталога в нашей базе данных
        $result_count = CatalogsService::whereHas('site', function ($query) use ($alias) {
            $query->whereAlias($alias);
        })
        ->whereCompany_id($request->user()->company_id)
        ->where($request->field, $request->value)
        ->where('id', '!=', $request->id)
        ->count();

        return response()->json($result_count);
    }

    /**
     * Получение каталогов торговой точки
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCatalogsForOutlet()
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
//        $answer = operator_right('catalogs_goods', true, getmethod('index'));

        $catalogsServices = CatalogsService::with([
            'items:id,catalogs_service_id,name,photo_id,parent_id',
            'prices' => function ($q) {
                $q->with([
                    'service' => function($q) {
                        $q->with([
                            'process' => function ($q) {
                                $q->with([
                                    'photo',
                                    'manufacturer'
                                ])
                                    ->where('draft', false)
                                    ->select([
                                        'id',
                                        'name',
                                        'photo_id',
                                        'manufacturer_id',
                                        'draft',
                                        'cost_default'
                                    ]);
                            },
                        ])
                            ->where('archive', false)
                            ->select([
                                'id',
                                'process_id',
                                'serial'
                            ]);
                    },
                    'currency',
                    'discounts_actual',
                    'catalogs_item.discounts_actual'
                ])
                    ->whereHas('service', function ($q) {
                        $q
//                        ->when($settings->firstWhere('alias', 'sale-for-order'), function ($q) {
//                        $q->where('is_ordered', true);
//                    })
//                    ->when($settings->firstWhere('alias', 'sale-for-production'), function ($q) {
//                        $q->where('is_produced', true);
//                    })
//                    ->when($settings->firstWhere('alias', 'sale-from-stock'), function ($q) {
//                        $q->whereHas('stocks', function ($q) {
//                            $q->where('filial_id', auth()->user()->StafferFilialId)
//                            ->where('free', '>', 0);
//                        });
//                    })
                            ->where('archive', false)
                            ->whereHas('process', function ($q) {
                                $q->where('draft', false);
                            });
                    })
                    ->where([
                        'archive' => false,
                        'filial_id' => request()->filial_id
                    ])
//                ->select([
//                    'prices_goods.id',
//                    'archive',
//                    'prices_goods.catalogs_goods_id',
//                    'catalogs_goods_item_id',
//                    'price',
//                    'goods_id',
//                    'filial_id'
//                ])
                ;
            },
        ])
//            ->moderatorLimit($answer)
//            ->companiesLimit($answer)
//            ->authors($answer)
//            ->filials($answer)
//        ->whereHas('filials', function ($q) {
//            $q->where('id', auth()->user()->stafferFilialId);
//        })
            ->whereHas('outlets', function ($q) {
                $q->where('id', request()->outlet_id);
            })
            ->get();
//         dd($catalogsServices);

        $success = false;
        if ($catalogsServices->isNotEmpty()) {
            $success = true;
        }

        $catalogsServicesItems = [];
        $catalogsServicesPrices = [];
        foreach ($catalogsServices as $catalogServices) {
            $catalogsServicesItems = array_merge($catalogsServicesItems, buildTreeArray($catalogServices->items));

            $catalogsServicesPrices = array_merge($catalogsServicesPrices, $catalogServices->prices->setAppends([
                'totalWithDiscounts',
            ])->toArray());
        }
//        dd($catalogsServicesPrices);

        $catalogsServicesData = [
            'success' => $success,
            'catalogsServices' => $catalogsServices,
            'catalogsServicesItems' => $catalogsServicesItems,
            'catalogsServicesPrices' => $catalogsServicesPrices
        ];

        return response()->json($catalogsServicesData);
    }
}
