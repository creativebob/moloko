<?php

namespace App\Http\Controllers;

// Модели
use App\Catalog;
use App\Site;
use App\CatalogProduct;
use App\Goods;
use App\Service;
use App\Raw;

// Валидация
use Illuminate\Http\Request;

class CatalogProductController extends Controller
{

    // Настройки сконтроллера
    public function __construct(CatalogProduct $catalog_product)
    {
        $this->middleware('auth');
        $this->catalog_product = $catalog_product;
        $this->class = CatalogProduct::class;
        $this->model = 'App\CatalogProduct';
        $this->entity_alias = with(new $this->class)->getTable();
        $this->entity_dependence = false;
    }

    public function index(Request $request, $alias)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_catalogs = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // -------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -------------------------------------------------------------------------------------------
        $catalogs = Catalog::with([
            'services' => function ($query) {
                $query->with('services_article');
            },
            'goods' => function ($query) {
                $query->with('goods_article');
            },
            'raws' => function ($query) {
                $query->with('raws_article');
            }
        ])
        ->withCount('services', 'goods', 'raws')
        ->whereHas('site', function ($q) use ($alias) {
            $q->whereAlias($alias);
        })
        ->paginate(30);
        dd($catalogs);

        // Инфо о странице
        $page_info = pageInfo($this->entity_alias);

        return view('catalog_products.index', compact('catalogs', 'page_info'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request, $alias)
    {
        //
    }


    public function show(Request $request, $alias, $id = null)
    {

        // dd($alias);
        // Подключение политики
        $this->authorize('index', $this->class);

        if ($id == null) {
            $catalog = Catalog::whereHas('site', function ($query) use ($alias) {
                $query->whereAlias($alias);
            })->first();

            if ($catalog) {
                $id = $catalog->id;
            } else {
                return redirect()->route('catalog.index', ['alias' => $alias]);
                // return redirect("/admin/sites/".$alias."/catalogs");
            }
        }

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // -------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -------------------------------------------------------------------------------------------
        $catalog = Catalog::with([
            'site' => function ($query) {
                $query->with([
                    'catalogs',
                    'company' => function ($query) {
                        $query->withCount('sites');
                    }
                ]);
            },
            'services' => function ($query) {
                $query->orderBy('catalog_products.sort', 'asc');
            },
            'goods' => function ($query) {
                $query->orderBy('catalog_products.sort', 'asc');
            },
            'raws' => function ($query) {
                $query->orderBy('catalog_products.sort', 'asc');
            }
        ])
        ->withCount('services', 'goods', 'raws')
        ->findOrFail($id);
        // dd($catalog);


        return view('catalog_products.show', [
            'catalog' => $catalog,
            'page_info' => pageInfo($this->entity_alias),
            'parent_page_info' => pageInfo('sites'),
            'site' => Site::moderatorLimit(operator_right('sites', $this->entity_dependence, getmethod(__FUNCTION__)))
            ->whereAlias($alias)
            ->first()
        ]);
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy(Request $request, $alias, $id)
    {

        $catalog_product = CatalogProduct::findOrFail($id);
        // dd($catalog_product);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $catalog_product);

        return response()->json($catalog_product->delete());

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
        $catalog = Catalog::with('goods', 'raws', 'services')->findOrFail($catalog_id);
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
        $catalog = Catalog::with('goods', 'raws', 'services')
        ->findOrFail($catalog_id);
        // return $catalog->count();

        $catalog->$product_type()->attach($product_id, ['display' => 1]);

        $catalog = Catalog::with([
            $product_type => function ($query) {
                $query->orderBy('catalog_products.sort', 'asc');
            }
        ])
        ->findOrFail($catalog_id);

        return view('catalog_products.content_core', compact('catalog'));
    }
}
