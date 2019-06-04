<?php

namespace App\Http\Controllers;

// Модели
use App\PricesService;

use App\CatalogsService;
use App\CatalogsServicesItem;

// Валидация
use Illuminate\Http\Request;
// use App\Http\Requests\CatalogsServiceRequest;

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
        // $this->authorize(getmethod(__FUNCTION__), $this->class);


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

    public function sync(Request $request, $catalog_id)
    {

        // dd($request);
        $prices_service = PricesService::with(['catalog', 'catalogs_item.category', 'catalogs_item.childs'])
        ->findOrFail($request->id);


        $user = $request->user();

        $catalog = CatalogsService::firstOrCreate([
            'name' => $prices_service->catalog->name,
            'company_id' => $user->company_id,
        ], [
            'display' => 1,
            'author_id' => hideGod($user),
        ]);
        // dd($catalog);

        if (isset($prices_service->catalogs_item->category_id)) {

            $category = $prices_service->catalogs_item->category;

            $category = CatalogsServicesItem::firstOrCreate([
                'catalogs_service_id' => $catalog->id,
                'name' => $category->name,
                'company_id' => $user->company_id,
                'category_id' => null,
                'parent_id' => null,
            ], [
                'display' => 1,
                'author_id' => hideGod($user),
            ]);

            $prices_service->catalogs_item->category->load('childs');

            function getChilds($item, $category, $user, $catalog, $parent_id) {

                $catalogs_services_item = CatalogsServicesItem::firstOrCreate([
                    'catalogs_service_id' => $catalog->id,
                    'name' => $item->name,
                    'company_id' => $user->company_id,
                    'category_id' => $category->id,
                    'parent_id' => $parent_id,
                ], [
                    'display' => 1,
                    'author_id' => hideGod($user),
                ]);

                $catalogs_services_item->load('childs');

                if ($item->childs->isNotEmpty()) {
                    foreach ($item->childs as $item) {
                        $catalog_item_id = getChilds($item, $category, $user, $catalog, $catalogs_services_item->id);
                        // dd($catalog_item_id);
                        return $catalog_item_id;
                    }
                } else {
                    $catalog_item_id = $catalogs_services_item->id;
                    // dd($catalog_item_id);
                    return $catalog_item_id;
                }
            }

            if ($prices_service->catalogs_item->category->childs->isNotEmpty()) {
                foreach ($prices_service->catalogs_item->category->childs as $item) {
                    $catalog_item_id = getChilds($item, $category, $user, $catalog, $category->id);
                    // dd($catalog_item_id);
                }
            } else {
                // dd('lol');
                $catalog_item_id = $category->id;
            }
        } else {
            $catalog_item_id = $prices_service->catalogs_item->id;
        }

        $service_id = $prices_service->service_id;

        $prices_service = PricesService::firstOrCreate([
            'catalogs_services_item_id' => $catalog_item_id,
            'catalogs_service_id' => $catalog->id,
            'service_id' => $service_id,
        ], [
            'display' => 1,
            'price' => $request->price,
            'author_id' => hideGod($user),
            'company_id' => $user->company_id,
        ]);
        // dd($prices_service);

        return view('prices_services.sync', compact('prices_service'));
    }
}
