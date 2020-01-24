<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\Photable;
use App\Http\Requests\CatalogsServicesItemUpdateRequest;
use App\Http\Requests\CatalogsServicesItemStoreRequest;
use App\CatalogsServicesItem;
use App\CatalogsService;
use Illuminate\Http\Request;

class CatalogsServicesItemController extends Controller
{

    /**
     * CatalogsServicesItemController constructor.
     * @param CatalogsServicesItem $catalogs_services_item
     */
    public function __construct(CatalogsServicesItem $catalogs_services_item)
    {
        $this->middleware('auth');
        $this->catalogs_services_item = $catalogs_services_item;
        $this->class = CatalogsServicesItem::class;
        $this->model = 'App\CatalogsServicesItem';
        $this->entity_alias = with(new $this->class)->getTable();
        $this->entity_dependence = false;
        $this->type = 'edit';
    }

    use Photable;

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param $catalog_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request, $catalog_id)
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

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

        $catalogs_services_items = CatalogsServicesItem::with('childs')
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer)
        ->where('catalogs_service_id', $catalog_id)
            ->orderBy('sort')
        ->get();
        // dd($catalogs_services_items);

        // Отдаем Ajax
        if ($request->ajax()) {

            return view('system.common.accordions.categories_list',
                [
                    'items' => $catalogs_services_items,
                    'entity' => $this->entity_alias,
                    'class' => $this->model,
                    'type' => $this->type,
                    'count' => $catalogs_services_items->count(),
                    'id' => $request->id,
                    'nested' => 'childs_count',
                ]
            );
        }

        $catalog_service = CatalogsService::findOrFail($catalog_id);

        // Отдаем на шаблон
        return view('catalogs_services_items.index', [
            'catalogs_services_items' => $catalogs_services_items,
            'page_info' => pageInfo($this->entity_alias),
            'id' => $request->id,
            'catalog_id' => $catalog_id,
            'catalog_service' => $catalog_service
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Request $request
     * @param $catalog_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create(Request $request, $catalog_id)
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        return view('system.common.accordions.create', [
            'item' => CatalogsService::make(),
            'entity' => $this->entity_alias,
            'title' => 'Добавление пункта каталога',
            'parent_id' => $request->parent_id,
            'category_id' => $request->category_id,
            'catalog_id' => $catalog_id,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CatalogsServicesItemStoreRequest $request
     * @param $catalog_id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(CatalogsServicesItemStoreRequest $request, $catalog_id)
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        $data = $request->input();
        $data['catalogs_service_id'] = $catalog_id;
        $catalogs_services_item = CatalogsServicesItem::create($data);

        if ($catalogs_services_item) {

            // Переадресовываем на index
            return redirect()->route('catalogs_services_items.index', ['catalog_id' => $catalog_id, 'id' => $catalogs_services_item->id]);

        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при записи пункта каталога!'
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
     * @param $catalog_id
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit(Request $request, $catalog_id, $id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $catalogs_services_item = CatalogsServicesItem::moderatorLimit($answer)
        ->findOrFail($id);
        // dd($catalogs_services_item);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $catalogs_services_item);

        $catalog_service = CatalogsService::findOrFail($catalog_id);

        return view('catalogs_services_items.edit', [
            'catalogs_services_item' => $catalogs_services_item,
            'catalog_id' => $catalog_id,
            'page_info' => pageInfo($this->entity_alias),
            'catalog_service' => $catalog_service
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param CatalogsServicesItemUpdateRequest $request
     * @param $catalog_id
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(CatalogsServicesItemUpdateRequest $request, $catalog_id, $id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $catalogs_services_item = CatalogsServicesItem::moderatorLimit($answer)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $catalogs_services_item);

        $data = $request->input();
        $data['photo_id'] = $this->getPhotoId($request, $catalogs_services_item);
        $result = $catalogs_services_item->update($data);

        if ($result) {

            $catalogs_services_item->filters()->sync($request->filters);

            // Переадресовываем на index
            return redirect()->route('catalogs_services_items.index', ['catalog_id' => $catalog_id, 'id' => $catalogs_services_item->id]);
        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при обновлени пункта меню!'
            ];
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param $catalog_id
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(Request $request, $catalog_id, $id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $catalogs_services_item = CatalogsServicesItem::moderatorLimit($answer)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $catalogs_services_item);

        $parent_id = $catalogs_services_item->parent_id;

        $catalogs_services_item->delete();

        if ($catalogs_services_item) {

            // Переадресовываем на index
            return redirect()->route('catalogs_services_items.index', ['catalog_id' => $catalog_id, 'id' => $parent_id]);

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
        // $catalog_id = 1;

        $text_fragment = $request->text_fragment;
        $catalog_id = $request->catalog_id;

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_goods = operator_right('goods', false, 'index');
        $answer_services = operator_right('services', false, 'index');
        $answer_raws = operator_right('raws', false, 'index');

        // --------------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // --------------------------------------------------------------------------------------------------------------
        $catalog = CatalogsService::with('goods', 'raws', 'services')->findOrFail($catalog_id);
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
        $catalog_id = $request->catalog_id;

        // $product_id = 1;
        // $product_type = 'services';
        // $catalog_id = 1;

        // Добавление связи
        $catalog = CatalogsService::with('goods', 'raws', 'services')
        ->findOrFail($catalog_id);
        // return $catalog->count();

        $catalog->$product_type()->attach($product_id, ['display' => 1]);

        $catalog = CatalogsService::with([
            $product_type => function ($query) {
                $query->orderBy('catalog_products.sort', 'asc');
            }
        ])
        ->findOrFail($catalog_id);

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
        ->findOrFail($request->id);
        // dd($catalogs_services_item);

        return view('leads.catalogs.prices_services', compact('catalogs_services_item'));
    }

    public function ajax_get(Request $request, $catalog_id)
    {
        return view('products.processes.services.prices.catalogs_items', compact('catalog_id'));
    }

}
