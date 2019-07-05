<?php

namespace App\Http\Controllers;

// Модели
use App\PricesService;

// Валидация
use Illuminate\Http\Request;


class PricesServiceController extends Controller
{
    // Настройки контроллера
    public function __construct(PricesService $prices_service)
    {
        $this->middleware('auth');
        $this->prices_service = $prices_service;
        $this->entity_alias = with(new PricesService)->getTable();;
        $this->entity_dependence = true;
        $this->class = PricesService::class;
        $this->model = 'App\PricesService';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $catalog_id)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

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
            'service.process',
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
//            ->filials($answer)
        // ->authors($answer)
        // ->systemItem($answer)
        ->where([
            'archive' => false,
            'catalogs_service_id' => $catalog_id,
            'filial_id' => $filial_id,
        ])
        ->paginate(30);
//         dd($prices_services);

        // -----------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ------------------------------------------------------------------------------
        // -----------------------------------------------------------------------------------------------------------

        // $filter = setFilter($this->entity_alias, $request, [
        //     'author',               // Автор записи
        //     // 'services_category',    // Категория услуги
        //     // 'services_product',     // Группа услуги
        //     // 'date_interval',     // Дата обращения
        //     'booklist'              // Списки пользователя
        // ]);


        // Инфо о странице
        $page_info = pageInfo($this->entity_alias);

        return view('prices_services.index', [
            'prices_services' => $prices_services,
            'page_info' => $page_info,
            'class' => $this->class,
            'entity' => $this->entity_alias,
            // 'filter' => $filter,
            'nested' => null,
            'catalog_id' => $catalog_id,
            'filial_id' => $filial_id
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, $catalog_id)
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        $filial_id = $request->filial_id;
        return view('prices_services.sync.modal', compact('catalog_id', 'filial_id'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $catalog_id, $id)
    {
        $price = PricesService::findOrFail($id);

        return view('prices_services.price_edit', ['price' => $price->price]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $catalog_id, $id)
    {
        $prices_service = PricesService::findOrFail($id);
        $price = $request->price;

        if ($prices_service->price == $price) {
            return view('prices_services.price', ['prices_service' => $prices_service]);
        } else {
            $new_prices_service = $prices_service->replicate();

            $prices_service->update([
                'archive' => true,
            ]);
            // dd($new_price);

            $new_prices_service->price = $price;
            $new_prices_service->save();

            // dd($price);
            return view('prices_services.price', ['prices_service' => $new_prices_service]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }


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
            abort(403, 'Ошиька архивирования');
        }
    }


    // --------------------------------- Ajax ----------------------------------------

    public function ajax_store(Request $request)
    {

        $data = $request->input();
        $prices_service = (new PricesService())->create($data);

        return view('products.processes.services.prices.price', compact('prices_service'));
    }



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

    public function ajax_edit(Request $request, $catalog_id)
    {
        $price = PricesService::findOrFail($request->id);
        // dd($price);
        return view('products.processes.services.prices.catalogs_item_edit', compact('price'));
    }

    public function ajax_update(Request $request, $catalog_id)
    {

        $prices_service = PricesService::findOrFail($request->id);

        if ($prices_service->price == $request->price) {
            return view('products.processes.services.prices.price', ['prices_service' => $prices_service]);
        } else {
            $new_prices_service = $prices_service->replicate();

            $prices_service->update([
                'archive' => true,
            ]);
            // dd($new_price);

            $new_prices_service->price = $request->price;
            $new_prices_service->save();

            // dd($price);
            return view('products.processes.services.prices.price', ['prices_service' => $new_prices_service]);
        }
    }

    public function ajax_archive(Request $request)
    {
        $user = $request->user();

        $result = PricesService::where('id', $request->id)->update([
            'archive' => true,
            'editor_id' => hideGod($user)
        ]);
        return response()->json($result);
    }
}
