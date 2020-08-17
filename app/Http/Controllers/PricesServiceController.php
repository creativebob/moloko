<?php

namespace App\Http\Controllers;

use App\CatalogsService;
use App\PricesService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class PricesServiceController extends Controller
{
    /**
     * PricesServiceController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->entity_alias = with(new PricesService)->getTable();;
        $this->entity_dependence = true;
        $this->class = PricesService::class;
        $this->model = 'App\PricesService';
    }

    /**
     * Отображение списка ресурсов.
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

        // Включение контроля активного фильтра
        $filter_url = autoFilter($request, $this->entity_alias);

        if (($filter_url != null)&&($request->filter != 'active')) {
            Cookie::queue(Cookie::forget('filter_' . $this->entity_alias));
            return Redirect($filter_url);
        }

        $user_filials = session('access.all_rights.index-prices_services-allow.filials');
//        $user_filials = session('access.all_rights.index-leads-allow');

        // dd($request);

        if (isset($request->filial_id)) {
            $filial_id = $request->filial_id;
        } else {
            if (!is_null($user_filials)) {
                $filial_id = key($user_filials);
            } else {
                $filial_id = null;
            }
        }

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $prices_services = PricesService::with([
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
            'catalogs_item'
        ])
//        ->whereHas('service', function ($q) {
//            $q->whereHas('process', function ($q) {
//                $q->where('draft', false);
//            })
//            ->where('archive', false);
//        })
        // ->moderatorLimit($answer)
         ->companiesLimit($answer)
        ->booklistFilter($request)

//        ->whereHas('catalogs_item', function($q) use ($request){
//            $q->filter($request, 'author_id');
//        })
//
//        ->whereHas('catalogs_item', function($q) use ($request){
//            $q->filter($request, 'catalogs_services_item_id');
//        })

        ->whereHas('service.process', function($q){
            $q->where('draft', false)
                ->where('archive', false);
        })
//            ->filials($answer)
        // ->authors($answer)
        // ->systemItem($answer)
        ->where([
            'archive' => false,
            'catalogs_service_id' => $catalog_id,
            'filial_id' => $filial_id,
        ])
        ->paginate(300);
//         dd($prices_services);

        // -----------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ------------------------------------------------------------------------------
        // -----------------------------------------------------------------------------------------------------------

        $filter = setFilter($this->entity_alias, $request, [
//            'author',                               // Автор записи
//            'booklist',                             // Списки пользователя
//            'catalogs_services_items'                  // Списки пользователя
        ]);

        // Окончание фильтра -----------------------------------------------------------------------------------------

        // Инфо о странице
        $pageInfo = pageInfo($this->entity_alias);

        $catalog = CatalogsService::with([
            'filials'
        ])
            ->findOrFail($catalog_id);
        $pageInfo->title = 'Прайс: ' . $catalog->name;
        $pageInfo->name = 'Прайс: ' . $catalog->name;

        return view('prices_services.index', [
            'prices_services' => $prices_services,
            'pageInfo' => $pageInfo,
            'class' => $this->class,
            'entity' => $this->entity_alias,
            'filter' => $filter,
            'nested' => null,
            'catalog_id' => $catalog_id,
            'catalog' => $catalog,
            'filial_id' => $filial_id,
            'catalog_services' => $catalog
        ]);
    }

    /**
     * Показать форму для создания нового ресурса.
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

        $filial_id = $request->filial_id;
        return view('syste.pages.catalogs.services.prices_services.sync.modal', compact('catalog_id', 'filial_id'));
    }

    /**
     * Сохранение созданного ресурса в хранилище.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Отображение указанного ресурса.
     */
    public function show($id)
    {
        //
    }

    /**
     * Показать форму для редактирования указанного ресурса.
     *
     * @param Request $request
     * @param $catalog_id
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Request $request, $catalogId, $id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $priceService = PricesService::with([
            'service.process'
        ])
            ->moderatorLimit($answer)
            ->findOrFail($id);
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $priceService);

        $catalogServices = CatalogsService::findOrFail($catalogId);

        return view('system.pages.catalogs.services.prices_services.edit', [
            'priceService' => $priceService,
            'catalogId' => $catalogId,
            'pageInfo' => pageInfo($this->entity_alias),
            'catalogServices' => $catalogServices
        ]);
    }

    /**
     * Обновление указанного ресурса в хранилище.
     *
     * @param Request $request
     * @param $catalog_id
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function update(Request $request, $catalogId, $id)
    {
        $priceService = PricesService::findOrFail($id);

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
        $priceService->update($data);

        return redirect()->route('prices_services.index', $catalogId);
    }

    /**
     * Удаление указанного ресурса из хранилища.
     *
     * @param $id
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Архивирование
     *
     * @param Request $request
     * @param $catalog_id
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function archive(Request $request, $catalog_id, $id)
    {
        $user = $request->user();

        $price_service = PricesService::findOrFail($id);

        $filial_id = $price_service->filial_id;

        $result = $price_service->update([
            'archive' => true,
            'editor_id' => hideGod($user)
        ]);

        if ($result) {
            // Переадресовываем на index
            return redirect()->route('prices_services.index', [
                'catalog_id' => $catalog_id,
                'filial_id' => $filial_id
            ]);
        } else {
            abort(403, 'Ошибка архивирования');
        }
    }


    // --------------------------------- Ajax ----------------------------------------

    public function sync(Request $request, $catalog_id)
    {

        $prices_ids = array_keys($request->prices);

        $filial_id = $request->filial_id;

        $prices_services = PricesService::with(['follower' => function ($q) use ($filial_id) {
            $q->where('filial_id', $filial_id);
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
                    $sync_prices_service->filial_id = $filial_id;
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
            'catalog_id' => $catalog_id,
            'filial_id' => $filial_id
        ]);
    }

    public function ajax_get(Request $request, $catalog_id, $id)
    {
        $prices_service = PricesService::findOrFail($id);
        // dd($price);
        $price = $prices_service->price;
        // dd($price);
        return view('products.processes.services.prices.catalogs_item_price', compact('price'));
    }

    public function ajax_store(Request $request)
    {

        $prices_service = PricesService::firstOrNew([
            'catalogs_services_item_id' => $request->catalogs_services_item_id,
            'catalogs_service_id' => $request->catalogs_service_id,
            'service_id' => $request->service_id,
            'filial_id' => $request->filial_id,
            'currency_id' => $request->currency_id,
        ], [
            'price' => $request->price
        ]);

        if ($prices_service->id) {
            $prices_service->update([
                'archive' => false
            ]);

            if ($prices_service->price != $request->price) {

                $prices_service->actual_price->update([
                    'end_date' => now(),
                ]);

                $prices_service->history()->create([
                    'price' => $request->price,
                    'currency_id' => $prices_service->currency_id,
                ]);

                $prices_service->update([
                    'price' => $request->price,
                ]);
            }

        } else {
            $prices_service->save();
        }


        return view('products.processes.services.prices.price', compact('prices_service'));
    }

    public function ajax_edit(Request $request, $catalog_id)
    {
        $price = PricesService::findOrFail($request->id);
        // dd($price);
        return view('products.processes.services.prices.catalogs_item_edit', compact('price'));
    }

    public function ajax_update(Request $request, $catalog_id)
    {

        $prices_service = PricesService::findOrFail($request->id);

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

        $result = PricesService::findOrFail($request->id)
            ->update([
            'archive' => true,
            'editor_id' => hideGod($user)
        ]);
        return response()->json($result);
    }

    public function ajax_status(Request $request)
    {
        $result = PricesService::findOrFail($request->id)->update([
            'status' => $request->status
        ]);
        return response()->json($result);
    }

    public function ajax_hit(Request $request)
    {
        $result = PricesService::findOrFail($request->id)->update([
            'is_hit' => $request->is_hit
        ]);
        return response()->json($result);
    }

    public function ajax_new(Request $request)
    {
        $result = PricesService::findOrFail($request->id)->update([
            'is_new' => $request->is_new
        ]);
        return response()->json($result);
    }
}
