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


        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $prices_services = PricesService::with(['service.process', 'catalog', 'catalogs_item'])
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
        ->where('archive', false)
        ->where('catalogs_service_id', $catalog_id)
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
            'catalog_id' => $catalog_id
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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

        $prices_service_common = PricesService::findOrFail($request->id);

        $user = $request->user();

        $prices_service = PricesService::firstOrCreate([
            'catalogs_services_item_id' => $prices_service_common->catalogs_services_item_id,
            'catalogs_service_id' => $prices_service_common->catalogs_service_id,
            'service_id' => $prices_service_common->service_id,
            'company_id' => $user->company_id,
        ], [
            'author_id' => $user->id,
            'price' => $request->price,
        ]);

        return view('prices_services.sync', compact('prices_service'));
    }

    public function ajax_edit(Request $request, $catalog_id)
    {   
        $price = PricesService::findOrFail($request->id);
        // dd($price);
        return view('products.processes.services.prices.catalogs_item_edit', compact('price'));
    }

    public function ajax_update(Request $request, $catalog_id)
    {   

        $user = $request->user();

        $price = PricesService::findOrFail($request->id);

        $new_price = $price->replicate();

        $price->update([
            'archive' => true,
            'editor_id' => hideGod($user)
        ]);

        // dd($new_price);

        $new_price->price = $request->price;
        $new_price->save();


        // dd($price);
        return view('products.processes.services.prices.catalogs_item_price', ['price' => $new_price->price]);
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
