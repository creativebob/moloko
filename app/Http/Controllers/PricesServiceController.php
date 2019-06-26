<?php

namespace App\Http\Controllers;

// Модели
use App\PricesService;

use App\CatalogsService;
use App\CatalogsServicesItem;

// Валидация
use Illuminate\Http\Request;
// use App\Http\Requests\CatalogsServiceRequest;

use Illuminate\Support\Facades\Log;

class PricesServiceController extends Controller
{
    // Настройки сконтроллера
    public function __construct(PricesService $prices_service)
    {
        $this->middleware('auth');
        $this->prices_service = $prices_service;
        $this->entity_alias = with(new PricesService)->getTable();;
        $this->entity_dependence = false;
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
        ->whereHas('service', function ($q) {
            $q->whereHas('process', function ($q) {
                $q->where('draft', false);
            })
            ->where('archive', false);
        })
        // ->moderatorLimit($answer)
        // ->companiesLimit($answer)
        // ->authors($answer)
        // ->systemItem($answer)
        ->where([
            'archive' => false,
            'catalogs_service_id' => $catalog_id,
            'filial_id' => $filial_id,
        ])
        ->paginate(30);
        // dd($prices_services);

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
        $price = PricesService::findOrFail($id);

        if ($price->price == $request->price) {
            return view('prices_services.price', ['prices_service' => $price]);
        } else {
            $new_price = $price->replicate();

            $price->update([
                'archive' => true,
            ]);
            // dd($new_price);

            $new_price->price = $request->price;
            $new_price->save();

            // dd($price);
            return view('prices_services.price', ['prices_service' => $new_price]);
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

        $prices_services = PricesService::find($prices_ids)->keyBy('id');
        // dd($request->prices);

        $data = [];
        foreach ($request->prices as $id => $price) {
            $prices_service = $prices_services[$id];

            if (is_null($price)) {
                $prices_service->update([
                    'archive' => true,
                ]);
            } else {
                if ($prices_service->price != $price) {
                    $new_prices_service = $prices_service->replicate();

                    $prices_service->update([
                        'archive' => true,
                    ]);

                    $new_prices_service->price = $price;
                    $new_prices_service->save();
                }
            }
        }

        // Переадресовываем на index
        return redirect()->route('prices_services.index', [
            'catalog_id' => $catalog_id,
            'filial_id' => $request->filial_id
        ]);
    }

    public function ajax_edit(Request $request, $catalog_id)
    {
        $price = PricesService::findOrFail($request->id);
        // dd($price);
        return view('products.processes.services.prices.catalogs_item_edit', compact('price'));
    }

    public function ajax_update(Request $request, $catalog_id)
    {

        $price = PricesService::findOrFail($request->id);

        if ($price->price == $request->price) {
            return view('products.processes.services.prices.catalogs_item_price', ['price' => $price->price]);
        } else {
            $new_price = $price->replicate();

            $price->update([
                'archive' => true,
            ]);
            // dd($new_price);

            $new_price->price = $request->price;
            $new_price->save();

            // dd($price);
            return view('products.processes.services.prices.catalogs_item_price', ['price' => $new_price->price]);
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
