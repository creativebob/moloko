<?php

namespace App\Http\Controllers;

// Модели
use App\CatalogsGoodsItem;
use App\CatalogsGoods;

// Валидация
use Illuminate\Http\Request;
use App\Http\Requests\CatalogsGoodsItemRequest;

// Подключаем трейт записи и обновления категорий
use App\Http\Controllers\Traits\CategoryControllerTrait;

// Транслитерация
use Illuminate\Support\Str;

class CatalogsGoodsItemController extends Controller
{

    // Настройки сконтроллера
    public function __construct(CatalogsGoodsItem $catalogs_goods_item)
    {
        $this->middleware('auth');
        $this->catalogs_goods_item = $catalogs_goods_item;
        $this->class = CatalogsGoodsItem::class;
        $this->model = 'App\CatalogsGoodsItem';
        $this->entity_alias = with(new $this->class)->getTable();
        $this->entity_dependence = false;
        $this->type = 'modal';
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
            'catalogs_goods_id',
            'name',
            'parent_id',
            'company_id',
            'sort',
            'display',
            'system_item',
            'moderation',
            'author_id'
        ];

        $catalogs_goods_items = CatalogsGoodsItem::with('childs')
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer)
        ->where('catalogs_goods_id', $catalog_id)
        ->get($columns);
        // dd($catalogs_goods_items);

        // Отдаем Ajax
        if ($request->ajax()) {

            return view('common.accordions.categories_list',
                [
                    'items' => $catalogs_goods_items,
                    'entity' => $this->entity_alias,
                    'class' => $this->model,
                    'type' => $this->type,
                    'count' => $catalogs_goods_items->count(),
                    'id' => $request->id,
                    'nested' => 'childs_count',
                ]
            );
        }

        // Отдаем на шаблон
        return view('catalogs_goods_items.index', [
            'catalogs_goods_items' => $catalogs_goods_items,
            'page_info' => pageInfo($this->entity_alias),
            'catalog_id' => $catalog_id,
        ]);
    }

    public function create(Request $request, $catalog_id)
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);
        // dd('lol');

        return view('common.accordions.create', [
            'item' => new $this->class,
            'entity' => $this->entity_alias,
            'title' => 'Добавление пункта каталога',
            'parent_id' => $request->parent_id,
            'category_id' => $request->category_id,
            'catalog_id' => $catalog_id,
        ]);
    }

    public function store(CatalogsGoodsItemRequest $request, $catalog_id)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Заполнение и проверка основных полей в трейте
        $catalogs_goods_item = $this->storeCategory($request);

        $catalogs_goods_item->catalogs_goods_id = $catalog_id;

        // $catalogs_goods_item->tag = empty($request->tag) ? Str::slug($request->name) : $request->tag;

        $catalogs_goods_item->save();

        if ($catalogs_goods_item) {

            // Переадресовываем на index
            return redirect()->route('catalogs_goods_items.index', ['catalog_id' => $catalog_id, 'id' => $catalogs_goods_item->id]);

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

        $catalogs_goods_item = CatalogsGoodsItem::moderatorLimit($answer)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $catalogs_goods_item);

        return view('common.accordions.edit', [
            'item' => $catalogs_goods_item,
            'entity' => $this->entity_alias,
            'title' => 'Редактирование пункта каталога',
            'parent_id' => $catalogs_goods_item->parent_id,
            'category_id' => $catalogs_goods_item->category_id,
            'catalog_id' => $catalog_id,
        ]);
    }

    public function update(CatalogsGoodsItemRequest $request, $catalog_id, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $catalogs_goods_item = CatalogsGoodsItem::moderatorLimit($answer)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $catalogs_goods_item);

        // Заполнение и проверка основных полей в трейте
        $catalogs_goods_item = $this->updateCategory($request, $catalogs_goods_item);

        // $catalogs_goods_item->tag = empty($request->tag) ? Str::slug($request->name) : $request->tag;

        $catalogs_goods_item->save();
        // dd($catalogs_goods_item);

        if ($catalogs_goods_item) {

            // Переадресовываем на index
            return redirect()->route('catalogs_goods_items.index', ['catalog_id' => $catalog_id, 'id' => $catalogs_goods_item->id]);
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
        $catalogs_goods_item = CatalogsGoodsItem::moderatorLimit($answer)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $catalogs_goods_item);

        // Скрываем бога
        $catalogs_goods_item->editor_id = hideGod($request->user());
        $catalogs_goods_item->save();

        $parent_id = $catalogs_goods_item->parent_id;

        $catalogs_goods_item->delete();

        if ($catalogs_goods_item) {

            // Переадресовываем на index
            return redirect()->route('catalogs_goods_items.index', ['catalog_id' => $catalog_id, 'id' => $parent_id]);

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
        $catalog = CatalogsGoods::with('goods', 'raws', 'services')->findOrFail($catalog_id);
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
        $catalog = CatalogsGoods::with('goods', 'raws', 'services')
        ->findOrFail($catalog_id);
        // return $catalog->count();

        $catalog->$product_type()->attach($product_id, ['display' => 1]);

        $catalog = CatalogsGoods::with([
            $product_type => function ($query) {
                $query->orderBy('catalog_products.sort', 'asc');
            }
        ])
        ->findOrFail($catalog_id);

        return view('catalog_products.content_core', compact('catalog'));
    }
}