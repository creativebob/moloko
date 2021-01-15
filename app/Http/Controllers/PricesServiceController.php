<?php

namespace App\Http\Controllers;

use App\CatalogsService;
use App\CatalogsServicesItem;
use App\Discount;
use App\Http\Controllers\System\Traits\Discountable;
use App\PricesService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class PricesServiceController extends Controller
{
    protected $entityAlias;
    protected $entityDependence;

    /**
     * PricesServiceController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->entityAlias = 'prices_services';
        $this->entityDependence = true;
    }

    use Discountable;

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
        $this->authorize(getmethod(__FUNCTION__), PricesService::class);

        // Включение контроля активного фильтра
        $filter_url = autoFilter($request, $this->entityAlias);

        if (($filter_url != null)&&($request->filter != 'active')) {
            Cookie::queue(Cookie::forget('filter_' . $this->entityAlias));
            return Redirect($filter_url);
        }

        $user_filials = session('access.all_rights.index-prices_services-allow.filials');
//        $user_filials = session('access.all_rights.index-leads-allow');

        // dd($request);

        if (isset($request->filial_id)) {
            $filialId = $request->filial_id;
        } else {
            if (!is_null($user_filials)) {
                $filialId = key($user_filials);
            } else {
                $filialId = null;
            }
        }

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        $pricesServices = PricesService::with([
            'service' => function ($q) {
                $q->with([
                    'process' => function ($q) {
                        $q->with([
                            'unit',
                            'group.unit'
                        ]);
                    }
                ]);
            },
            'catalog',
            'catalogs_item',
            'discount_price',
            'discount_catalogs_item'
        ])
            ->withCount('likes')
        // ->moderatorLimit($answer)
         ->companiesLimit($answer)
            ->filter()

        ->booklistFilter($request)

//        ->whereHas('catalogs_item', function($q) use ($request){
//            $q->filter($request, 'author_id');
//        })
//
//        ->whereHas('catalogs_item', function($q) use ($request){
//            $q->filter($request, 'catalogs_services_item_id');
//        })

        ->whereHas('service', function($q){
                $q->whereHas('process', function ($q) {
                    $q->where('draft', false);
                })
                    ->where('archive', false);
            })
//            ->filials($answer)
        // ->authors($answer)
        // ->systemItem($answer)
        ->where([
            'archive' => false,
            'catalogs_service_id' => $catalogId,
            'filial_id' => $filialId,
        ])
        ->paginate(300);
//         dd($pricesServices);

        // -----------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ------------------------------------------------------------------------------
        // -----------------------------------------------------------------------------------------------------------

        $filter = setFilter($this->entityAlias, $request, [
//            'author',                               // Автор записи
//            'booklist',                             // Списки пользователя
//            'catalogs_services_items'                  // Списки пользователя
        ]);

        // Окончание фильтра -----------------------------------------------------------------------------------------

        // Инфо о странице
        $pageInfo = pageInfo($this->entityAlias);

        $catalogServices = CatalogsService::with([
            'filials'
        ])
            ->find($catalogId);

        $pageInfo->title = 'Прайс: ' . $catalogServices->name;
        $pageInfo->name = 'Прайс: ' . $catalogServices->name;

        return view('system.pages.catalogs.services.prices_services.index', [
            'pricesServices' => $pricesServices,
            'pageInfo' => $pageInfo,
            'class' => PricesService::class,
            'entity' => $this->entityAlias,
            'filter' => $filter,
            'nested' => null,
            'catalog' => $catalogServices,
            'filial_id' => $filialId,
            'catalogServices' => $catalogServices
        ]);
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
        $this->authorize(getmethod(__FUNCTION__), PricesService::class);

        $filial_id = $request->filial_id;
        return view('system.pages.catalogs.services.prices_services.sync.modal', compact('catalog_id', 'filial_id'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $catalogId
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($catalogId, $id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        $priceService = PricesService::with([
            'service.process',
            'currency',
            'discounts',
            'discounts_actual'
        ])
            ->moderatorLimit($answer)
            ->find($id);
//        dd($priceService);
        if (empty($priceService)) {
            abort(403, __('errors.not_found'));
        }
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $priceService);

        $catalogServices = CatalogsService::find($catalogId);

        // Инфо о странице
        $pageInfo = pageInfo($this->entityAlias);

        return view('system.pages.catalogs.services.prices_services.edit', compact('priceService', 'pageInfo', 'catalogId', 'catalogServices'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param $catalogId
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function update(Request $request, $catalogId, $id)
    {
        $priceService = PricesService::find($id);
//        dd($priceService);
        if (empty($priceService)) {
            abort(403, __('errors.not_found'));
        }

        $price = $request->price;

        if ($priceService->price != $price) {

            $priceService->actual_price->update([
                'end_date' => now(),
            ]);

            $priceService->history()->create([
                'price' => $price,
            ]);

            $priceService->update([
                'price' => $price,
            ]);
        }

        $data = $request->input();

        if ($request->ajax()) {
            if ($priceService->is_discount == 1) {
                $discountPrice = $priceService->discounts_actual->first();
                $data['price_discount_id'] = $discountPrice->id ?? null;

                $discountCatalogsItem = $priceService->catalogs_item->discounts_actual->first();
                $data['catalogs_item_discount_id'] = $discountCatalogsItem->id ?? null;
            } else {
                $data['price_discount_id'] = null;
                $data['catalogs_item_discount_id'] = null;
            }
        } else {
            $priceService->discounts()->sync($request->discounts);

            if ($request->is_discount == 1) {
                $priceService->load([
                    'discounts_actual',
                    'catalogs_item.discounts_actual'
                ]);

                $discountPrice = $priceService->discounts_actual->first();
                $data['price_discount_id'] = $discountPrice->id ?? null;

                $discountCatalogsItem = $priceService->catalogs_item->discounts_actual->first();
                $data['catalogs_item_discount_id'] = $discountCatalogsItem->id ?? null;
            } else {
                $data['price_discount_id'] = null;
                $data['catalogs_item_discount_id'] = null;
            }
        }

        $priceService->update($data);

        // Отдаем Ajax
        if ($request->ajax()) {
            $priceService = PricesService::with([
                'catalog',
                'catalogs_item.parent.parent',
                'filial',
                'currency'
            ])
                ->find($id);

            return response()->json($priceService);
        }

        return redirect()->route('prices_services.index', $catalogId);
    }

    /**
     * Архивирование
     *
     * @param Request $request
     * @param $catalogId
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function archive(Request $request, $catalogId, $id)
    {
        $user = $request->user();

        $priceService = PricesService::find($id);
//        dd($priceService);
        if (empty($priceService)) {
            abort(403, __('errors.not_found'));
        }

        $filialId = $priceService->filial_id;

        $result = $priceService->update([
            'archive' => true,
            'editor_id' => hideGod($user)
        ]);

        if ($result) {
            // Переадресовываем на index
            return redirect()->route('prices_services.index', [
                'catalog_id' => $catalogId,
                'filial_id' => $filialId
            ]);
        } else {
            abort(403, __('errors.archive'));
        }
    }


    // --------------------------------- Ajax ----------------------------------------

    public function sync(Request $request, $catalogId)
    {

        $prices_ids = array_keys($request->prices);

        $filialId = $request->filial_id;

        $prices_services = PricesService::with(['follower' => function ($q) use ($filialId) {
            $q->where('filial_id', $filialId);
        }])
        ->find($prices_ids)
            ->keyBy('id');

        foreach ($request->prices as $id => $price) {
            $prices_service = $prices_services[$id];

            // Если не пустая цена
            if (!is_null($price)) {

                // Если есть последователь
                if (!is_null($prices_service->follower)) {

                    // Сравниваем цену
                    if ($price != $prices_service->follower->price) {
                        $new_prices_service = $prices_service->follower->replicate();
                        $prices_service->follower->update([
                            'archive' => true
                        ]);

                        $new_prices_service->price = $price;
                        $new_prices_service->save();
                    }
                } else {
                    // Если последователя нет, то создаем
                    $sync_prices_service = $prices_service->replicate();

                    $sync_prices_service->ancestor_id = $prices_service->id;
                    $sync_prices_service->price = $price;
                    $sync_prices_service->filial_id = $filialId;
                    $sync_prices_service->save();
                }
            } else {
                // Если цена пустая
                // Если есть последователь, то архивируем
                if (!is_null($prices_service->follower)) {
                    $prices_service->follower->update([
                        'archive' => true
                    ]);
                }
            }
        }

        // Переадресовываем на index
        return redirect()->route('prices_services.index', [
            'catalog_id' => $catalogId,
            'filial_id' => $filialId
        ]);
    }

    public function ajax_get(Request $request, $catalogId, $id)
    {
        $prices_service = PricesService::find($id);
        // dd($price);
        $price = $prices_service->price;
        // dd($price);
        return view('products.processes.services.prices.catalogs_item_price', compact('price'));
    }

    public function ajax_store(Request $request)
    {
        $priceService = PricesService::where([
            'catalogs_services_item_id' => $request->catalogs_services_item_id,
            'catalogs_service_id' => $request->catalogs_service_id,
            'service_id' => $request->service_id,
            'filial_id' => $request->filial_id,
            'currency_id' => $request->currency_id,
        ])
            ->first();

        $catalogsServicesItem = CatalogsServicesItem::find($request->catalogs_services_item_id);

        $discountCatalogsItemId = null;
        if ($catalogsServicesItem) {
            $discountCatalogsItem = $catalogsServicesItem->discounts_actual->first();

            if ($discountCatalogsItem) {
                $discountCatalogsItemId = $discountCatalogsItem->id;
            }
        }

        $discountEstimate = Discount::where([
            'company_id' => auth()->user()->company_id,
            'archive' => false
        ])
            ->whereHas('entity', function ($q) {
                $q->where('alias', 'estimates');
            })
            ->where('begined_at', '<=', now())
            ->where(function ($q) {
                $q->where('ended_at', '>=', now())
                    ->orWhereNull('ended_at');
            })
            ->first();

        $discountEstimateId = $discountEstimate->id ?? null;

        if ($priceService) {

            if ($priceService->price != $request->price) {
                $priceService->actual_price->update([
                    'end_date' => now(),
                ]);
                $priceService->history()->create([
                    'price' => $request->price,
                    'currency_id' => $priceService->currency_id,
                ]);
            }

            $priceService->update([
                'price' => $request->price,
                'is_discount' => 1,
                'catalogs_item_discount_id' => $discountCatalogsItemId,
                'estimate_discount_id' => $discountEstimateId,
                'archive' => false
            ]);

        } else {
            $data = $request->input();
            $data['is_discount'] = 1;
            $data['catalogs_item_discount_id'] = $discountCatalogsItemId;
            $data['estimate_discount_id'] = $discountEstimateId;
//            return $data;
            $priceService = PricesService::create($data);
        }

        $priceService->load([
            'catalog',
            'catalogs_item.parent.parent',
            'filial',
            'currency'
        ]);

        return response()->json($priceService);
    }

    public function ajax_edit(Request $request, $catalogId)
    {
        $price = PricesService::find($request->id);
        // dd($price);
        return view('products.processes.services.prices.catalogs_item_edit', compact('price'));
    }

    public function ajax_update(Request $request, $catalogId)
    {

        $prices_service = PricesService::find($request->id);

        if ($prices_service->price != $request->price) {

            $prices_service->actual_price->update([
                'end_date' => Carbon::now(),
            ]);

            $prices_service->history()->create([
                'price' => $request->price,
                'currency_id' => $prices_service->currency_id,
            ]);

            $prices_service->update([
                'price' => $request->price,
            ]);
        }
        return view('products.processes.services.prices.price', compact('prices_service'));
    }

    public function ajax_archive(Request $request)
    {
        $user = $request->user();

        $result = PricesService::find($request->id)
            ->update([
            'archive' => true,
            'editor_id' => hideGod($user)
        ]);
        return response()->json($result);
    }

    public function ajax_status(Request $request)
    {
        $result = PricesService::find($request->id)->update([
            'status' => $request->status
        ]);
        return response()->json($result);
    }

    public function ajax_hit(Request $request)
    {
        $result = PricesService::find($request->id)->update([
            'is_hit' => $request->is_hit
        ]);
        return response()->json($result);
    }

    public function ajax_new(Request $request)
    {
        $result = PricesService::find($request->id)->update([
            'is_new' => $request->is_new
        ]);
        return response()->json($result);
    }
}
