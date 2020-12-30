<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\Photable;
use App\Http\Requests\System\CatalogsServicesItemUpdateRequest;
use App\Http\Requests\System\CatalogsServicesItemStoreRequest;
use App\CatalogsServicesItem;
use App\CatalogsService;
use Illuminate\Http\Request;

class CatalogsServicesItemController extends Controller
{
    protected $entityAlias;
    protected $entityDependence;

    /**
     * CatalogsServicesItemController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->model = 'App\CatalogsServicesItem';
        $this->entityAlias = 'catalogs_services_items';
        $this->entityDependence = false;
        $this->type = 'page';
    }

    use Photable;

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param $catalogId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request, $catalogId)
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), CatalogsServicesItem::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        $columns = [
            'id',
            'catalogs_service_id',
            'name',
            'parent_id',
            'company_id',
            'sort',
            'display',
            'system',
            'moderation',
            'author_id'
        ];

        $catalogsServicesItems = CatalogsServicesItem::with('childs')
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer)
        ->where('catalogs_service_id', $catalogId)
            ->orderBy('sort')
        ->get();
        // dd($catalogsServicesItems);

        // Отдаем Ajax
        if ($request->ajax()) {

            return view('system.common.categories.index.categories_list',
                [
                    'items' => $catalogsServicesItems,
                    'entity' => $this->entityAlias,
                    'class' => $this->model,
                    'type' => $this->type,
                    'count' => $catalogsServicesItems->count(),
                    'id' => $request->id,
                    'nested' => 'childs_count',
                ]
            );
        }

        $catalogServices = CatalogsService::find($catalogId);

        $id = $request->id;

        // Инфо о странице
        $pageInfo = pageInfo($this->entityAlias);

        // Отдаем на шаблон
        return view('system.pages.catalogs.services.catalogs_services_items.index', compact('catalogsServicesItems', 'pageInfo', 'id', 'catalogServices'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Request $request
     * @param $catalogId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create(Request $request, $catalogId)
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), CatalogsServicesItem::class);

        return view('system.common.categories.create.modal.create', [
            'item' => CatalogsService::make(),
            'entity' => $this->entityAlias,
            'title' => 'Добавление пункта каталога',
            'parent_id' => $request->parent_id,
            'category_id' => $request->category_id,
            'catalogId' => $catalogId,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CatalogsServicesItemStoreRequest $request
     * @param $catalogId
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(CatalogsServicesItemStoreRequest $request, $catalogId)
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), CatalogsServicesItem::class);

        $data = $request->input();
        $data['catalogs_service_id'] = $catalogId;
        $catalogsServicesItem = CatalogsServicesItem::create($data);

        if ($catalogsServicesItem) {

            // Переадресовываем на index
            return redirect()->route('catalogs_services_items.index', ['catalogId' => $catalogId, 'id' => $catalogsServicesItem->id]);

        } else {
            $result = [
                'error_status' => 1,
                'error_message' => __('errors.store')
            ];
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $catalogId
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($catalogId, $id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        $catalogsServicesItem = CatalogsServicesItem::moderatorLimit($answer)
        ->find($id);
        // dd($catalogsServicesItem);
        if (empty($catalogsServicesItem)) {
            abort(403, __('errors.not_found'));
        }

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $catalogsServicesItem);

        $catalogsServicesItem->load([
            'discounts'
        ]);

        $catalogServices = CatalogsService::find($catalogId);

        // Инфо о странице
        $pageInfo = pageInfo($this->entityAlias);

        return view('system.pages.catalogs.services.catalogs_services_items.edit', compact('catalogsServicesItem', 'pageInfo', 'catalogServices'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param CatalogsServicesItemUpdateRequest $request
     * @param $catalogId
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(CatalogsServicesItemUpdateRequest $request, $catalogId, $id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $catalogsServicesItem = CatalogsServicesItem::moderatorLimit($answer)
        ->find($id);
        // dd($catalogsServicesItem);
        if (empty($catalogsServicesItem)) {
            abort(403, __('errors.not_found'));
        }

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $catalogsServicesItem);

        $data = $request->input();
        $data['photo_id'] = $this->getPhotoId($catalogsServicesItem);
        $result = $catalogsServicesItem->update($data);

        if ($result) {

            $catalogsServicesItem->filters()->sync($request->filters);

            $catalogsServicesItem->discounts()->sync($request->discounts);

            if ($request->is_discount == 1) {
                $catalogsServicesItem->load([
                    'discounts_actual',
                    'prices_services_actual'
                ]);
                $discountCatalogsItem = $catalogsServicesItem->discounts_actual->first();
                if ($discountCatalogsItem) {
                    foreach($catalogsServicesItem->prices_services_actual as $priceService) {
                        $priceService->update([
                            'is_need_recalculate' => true
//                            'catalogs_item_discount_id' => $discountCatalogsItem->id ? $discountCatalogsItem->id : null
                        ]);
                    }
                } else {
                    foreach($catalogsServicesItem->prices_services_actual as $priceService) {
                        $priceService->update([
                            'is_need_recalculate' => true
//                            'catalogs_item_discount_id' => null
                        ]);
                    }
                }
            } else {
                foreach($catalogsServicesItem->prices_services_actual as $priceService) {
                    $priceService->update([
                        'is_need_recalculate' => true
//                        'catalogs_item_discount_id' => null
                    ]);
                }
            }

            // Переадресовываем на index
            return redirect()->route('catalogs_services_items.index', ['catalogId' => $catalogId, 'id' => $catalogsServicesItem->id]);
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
     * @param Request $request
     * @param $catalogId
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(Request $request, $catalogId, $id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $catalogsServicesItem = CatalogsServicesItem::moderatorLimit($answer)
        ->find($id);
        // dd($catalogsServicesItem);
        if (empty($catalogsServicesItem)) {
            abort(403, __('errors.not_found'));
        }

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $catalogsServicesItem);

        $parent_id = $catalogsServicesItem->parent_id;

        $catalogsServicesItem->delete();

        if ($catalogsServicesItem) {

            // Переадресовываем на index
            return redirect()->route('catalogs_services_items.index', ['catalogId' => $catalogId, 'id' => $parent_id]);

        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при удалении!'
            ];
        }

    }

    // ------------------------------------------------ Ajax -------------------------------------------------

    public function search_add_product(Request $request)
    {

        // Подключение политики
        // $this->authorize('index', Goods::class);

        // $text_fragment = 'тест';
        // $catalogId = 1;

        $text_fragment = $request->text_fragment;
        $catalogId = $request->catalog_id;

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_goods = operator_right('goods', false, 'index');
        $answer_services = operator_right('services', false, 'index');
        $answer_raws = operator_right('raws', false, 'index');

        // --------------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // --------------------------------------------------------------------------------------------------------------
        $catalog = CatalogsService::with('goods', 'raws', 'services')->find($catalogId);
        // dd($catalog->goods->keyBy('id')->toArray());

        $result_search_goods = Goods::with('goods_article')
        ->moderatorLimit($answer_goods)
        ->companiesLimit($answer_goods)
        ->authors($answer_goods)
        ->systemItem($answer_goods) // Фильтр по системным записям
        ->whereHas('goods_article', function ($query) use ($text_fragment){
            $query->whereNull('archive')
            ->where('name', 'LIKE', '%'.$text_fragment.'%');
        })
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->get();

        $result_search_goods = $result_search_goods->diff($catalog->goods);
        // dd($result_search_goods);

        $result_search_services = Service::with('services_article')
        ->moderatorLimit($answer_services)
        ->companiesLimit($answer_services)
        ->authors($answer_services)
        ->systemItem($answer_services) // Фильтр по системным записям
        ->whereHas('services_article', function ($query) use ($text_fragment){
            $query->whereNull('archive')
            ->where('name', 'LIKE', '%'.$text_fragment.'%');
        })
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->get();

        $result_search_services = $result_search_services->diff($catalog->services);

        $result_search_raws = Raw::with('raws_article')
        ->moderatorLimit($answer_raws)
        ->companiesLimit($answer_raws)
        ->authors($answer_raws)
        ->systemItem($answer_raws) // Фильтр по системным записям
        ->whereHas('raws_article', function ($query) use ($text_fragment){
            $query->whereNull('archive')
            ->where('name', 'LIKE', '%'.$text_fragment.'%');
        })
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->get();

        $result_search_raws = $result_search_raws->diff($catalog->raws);

        if(
            ($result_search_goods->count())||
            ($result_search_services->count())||
            ($result_search_raws->count())
        ){

            return view('catalog_products.search-add-product', compact('result_search_goods', 'result_search_services', 'result_search_raws'));
        } else {

            return view('catalog_products.search-add-product');
        }
    }

    public function add_product(Request $request)
    {

        $product_id = $request->product_id;
        $product_type = $request->product_type;
        $catalogId = $request->catalog_id;

        // $product_id = 1;
        // $product_type = 'services';
        // $catalogId = 1;

        // Добавление связи
        $catalog = CatalogsService::with('goods', 'raws', 'services')
        ->find($catalogId);
        // return $catalog->count();

        $catalog->$product_type()->attach($product_id, ['display' => 1]);

        $catalog = CatalogsService::with([
            $product_type => function ($query) {
                $query->orderBy('catalog_products.sort', 'asc');
            }
        ])
        ->find($catalogId);

        return view('catalog_products.content_core', compact('catalog'));
    }

    public function get_prices(Request $request)
    {

        $filial_id = $request->user()->filial_id;

        $catalogs_services_item = CatalogsServicesItem::with([
            'prices_services' => function ($q) use ($filial_id) {
                $q->where('archive', false)
                    ->where('filial_id', $filial_id)
                ->whereHas('service', function ($q) {
                    $q->where('archive', false)
                        ->whereHas('process', function ($q) {
                            $q->where('draft', false);
                        });
                    });
                }
            ])
        ->find($request->id);
        // dd($catalogs_services_item);

        return view('leads.catalogs.prices_services', compact('catalogs_services_item'));
    }

    public function ajax_get(Request $request, $catalogId)
    {
        return view('products.processes.services.prices.catalogs_items', compact('catalog_id'));
    }

}
