<?php

namespace App\Http\Controllers;

// Модели
use App\CatalogsServicesItem;
use App\CatalogsService;

// Валидация
use Illuminate\Http\Request;
use App\Http\Requests\CatalogsServicesItemRequest;

// Подключаем трейт записи и обновления категорий
use App\Http\Controllers\Traits\CategoryControllerTrait;

// Транслитерация
use Illuminate\Support\Str;

class CatalogsServicesItemController extends Controller
{

    // Настройки сконтроллера
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

    // Используем трейт записи и обновления категорий
    use CategoryControllerTrait;

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
            'system_item',
            'moderation',
            'author_id'
        ];

        $catalogs_services_items = CatalogsServicesItem::with('childs')
        // ->where(function($q) use ($catalog_id, $answer) {
        //     $q->where('catalogs_service_id', $catalog_id)
        //     ->template($answer);
        // })
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer)
        // ->where('catalogs_service_id', $catalog_id)
        // ->template($answer)
        ->where('catalogs_service_id', $catalog_id)
        ->get($columns);
        // dd($catalogs_services_items);

        // Отдаем Ajax
        if ($request->ajax()) {

            return view('common.accordions.categories_list',
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

        // Отдаем на шаблон
        return view('catalogs_services_items.index', [
            'catalogs_services_items' => $catalogs_services_items,
            'page_info' => pageInfo($this->entity_alias),
            'catalog_id' => $catalog_id,
        ]);
    }

    public function create(Request $request, $catalog_id)
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        return view('common.accordions.create', [
            'item' => new $this->class,
            'entity' => $this->entity_alias,
            'title' => 'Добавление пункта каталога',
            'parent_id' => $request->parent_id,
            'category_id' => $request->category_id,
            'catalog_id' => $catalog_id,
            'page_info' => pageInfo($this->entity_alias),
        ]);
    }

    public function store(CatalogsServicesItemRequest $request, $catalog_id)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Заполнение и проверка основных полей в трейте
        $catalogs_services_item = $this->storeCategory($request);

        $catalogs_services_item->catalogs_service_id = $catalog_id;

        // $catalogs_services_item->tag = empty($request->tag) ? Str::slug($request->name) : $request->tag;

        $catalogs_services_item->save();

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


    public function show($id)
    {
        //
    }

    public function edit(Request $request, $catalog_id, $id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $catalogs_services_item = CatalogsServicesItem::moderatorLimit($answer)
        ->findOrFail($id);
        // dd($catalogs_services_item);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $catalogs_services_item);

        return view('catalogs_services_items.edit', [
            'catalogs_services_item' => $catalogs_services_item,
            'catalog_id' => $catalog_id,
            'page_info' => pageInfo($this->entity_alias),
        ]);
    }

    public function update(CatalogsServicesItemRequest $request, $catalog_id, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $catalogs_services_item = CatalogsServicesItem::moderatorLimit($answer)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $catalogs_services_item);

        // Заполнение и проверка основных полей в трейте
        $catalogs_services_item = $this->updateCategory($request, $catalogs_services_item);

        // $catalogs_services_item->tag = empty($request->tag) ? Str::slug($request->name) : $request->tag;

        $catalogs_services_item->save();
        // dd($catalogs_services_item);

        if ($catalogs_services_item) {

            // Переадресовываем на index
            return redirect()->route('catalogs_services_items.index', ['catalog_id' => $catalog_id, 'id' => $catalogs_services_item->id]);
        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при обновлени пункта меню!'
            ];
        }
    }

    public function destroy(Request $request, $catalog_id, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $catalogs_services_item = CatalogsServicesItem::moderatorLimit($answer)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $catalogs_services_item);

        // Скрываем бога
        $catalogs_services_item->editor_id = hideGod($request->user());
        $catalogs_services_item->save();

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
