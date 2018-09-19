<?php

namespace App\Http\Controllers;

// Модели
use App\Catalog;
use App\CatalogProduct;
use App\Goods;
use App\Service;
use App\Raw;

// Политика
use App\Policies\CatalogProductPolicy;

// Общие классы
use Illuminate\Support\Facades\Log;

// Специфические классы 

// На удаление
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;

class CatalogProductController extends Controller
{

    // Сущность над которой производит операции контроллер
    protected $entity_name = 'catalog_products';
    protected $entity_dependence = false;

    public function index(Request $request, $alias)
    {

        // dd($alias);
        // Подключение политики
        $this->authorize('index', CatalogProduct::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_catalogs = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // -------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -------------------------------------------------------------------------------------------
        $catalogs = Catalog::with(['services' => function ($query) {
            $query->with('services_article', 'catalogs');
        }])
        ->whereSite_id(2)
        ->paginate(30);
        // dd($catalogs);

        // Сущность
        $entity = $this->entity_name;

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        return view('catalog_products.index', compact('catalogs', 'page_info'));
    }


    public function search_add_product($text_fragment, $catalog_id)
    {

        // Подключение политики
        $this->authorize('index', Goods::class);

        // $text_fragment = 'това';
        // $catalog_id = 1;

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_goods = operator_right('goods', false, 'index');
        $answer_services = operator_right('services', false, 'index');
        $answer_raws = operator_right('raws', false, 'index');    

        // --------------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // --------------------------------------------------------------------------------------------------------------
        $catalog = Catalog::with('goods', 'services', 'raws')->findOrFail($catalog_id);
        // dd($catalog->goods->keyBy('id')->toArray());

        $result_search_goods = Goods::with('goods_article')
        ->moderatorLimit($answer_goods)
        ->companiesLimit($answer_goods)
        ->authors($answer_goods)
        ->systemItem($answer_goods) // Фильтр по системным записям
        ->whereHas('goods_article', function ($query) use ($text_fragment){
            $query->where('name', 'LIKE', '%'.$text_fragment.'%');
        })
        ->whereNull('archive')
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
            $query->where('name', 'LIKE', '%'.$text_fragment.'%');
        })
        ->whereNull('archive')
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
            $query->where('name', 'LIKE', '%'.$text_fragment.'%');
        })
        ->whereNull('archive')
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
        $this->authorize('index', CatalogProduct::class);

        if ($id == null) {
            $catalog = Catalog::whereHas('site', function ($query) use ($alias) {
                $query->whereAlias($alias);
            })->first();

            if ($catalog) {
                $id = $catalog->id;
            } else {
                return redirect("/admin/sites/".$alias."/catalogs");
            }
        }

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // -------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -------------------------------------------------------------------------------------------
        $catalog = Catalog::with(['site' => function ($query) {
            $query->with(['catalogs', 'company' => function ($query) {
                $query->withCount('sites');
            }]);
        }, 'services' => function ($query) {
            $query->orderBy('catalog_products.sort', 'asc');
        }])
        ->findOrFail($id);
        // d($catalog);

        $site = $catalog->site;
        // dd($site->catalogs->toArray());

        // Функция отрисовки списка со вложенностью и выбранным родителем (Отдаем: МАССИВ записей, Id родителя записи, параметр блокировки категорий (1 или null), запрет на отображенеи самого элемента в списке (его Id))
        $catalogs_list = get_select_tree($site->catalogs->keyBy('id')->toArray(), $catalog->id, null, null);
        // dd($catalogs_list);

        // Сущность
        $entity = $this->entity_name;

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        // Так как сущность имеет определенного родителя
        $parent_page_info = pageInfo('sites');

        return view('catalog_products.show', compact('catalog', 'catalogs_list', 'site', 'page_info', 'parent_page_info', 'alias', 'entity'));
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

        // ГЛАВНЫЙ ЗАПРОС:
        $catalog_product = CatalogProduct::findOrFail($id);
        // dd($catalog_product);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $catalog_product);

        if ($catalog_product) {

            $catalog_product_id = $catalog_product->catalog->id;

            // Удаляем новость с обновлением
            $catalog_product = CatalogProduct::destroy($id);

            if ($catalog_product) {
                $result = [
                    'error_status' => 0,
                ];
            } else {
                $result = [
                    'error_status' => 1,
                    'error_message' => 'Ошибка при удалении продукции из каталога!'
                ];
            }

            echo json_encode($result, JSON_UNESCAPED_UNICODE);
        } else {
            abort(403, 'Ошибка при удалении продукции из каталога!');
        }
    }

    // ------------------------------------------------ Ajax -------------------------------------------------

    // Сортировка
    public function ajax_sort(Request $request)
    {
        
        $i = 1;
        foreach ($request->catalog_products as $item) {
            CatalogProduct::where('id', $item)->update(['sort' => $i]);
            $i++;
        }
    }

    // Системная запись
    public function ajax_system_item(Request $request)
    {

        if ($request->action == 'lock') {
            $system = 1;
        } else {
            $system = null;
        }

        $item = CatalogProduct::where('id', $request->id)->update(['system_item' => $system]);

        if ($item) {

            $result = [
                'error_status' => 0,
            ];  
        } else {

            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при обновлении статуса системной записи!'
            ];
        }
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    // Отображение на сайте
    public function ajax_display(Request $request)
    {

        if ($request->action == 'hide') {
            $display = null;
        } else {
            $display = 1;
        }

        $item = CatalogProduct::where('id', $request->id)->update(['display' => $display]);

        if ($item) {
            $result = [
                'error_status' => 0,
            ];  
        } else {

            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при обновлении отображения на сайте!'
            ];
        }
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    public function add_product(Request $request)
    {

        $product_id = $request->product_id;
        $product_type = $request->product_type;
        $catalog_id = $request->catalog_id;

        // $id = 1;
        // $product_type = 'services';
        // $catalog_id = 1;  

        // Подключение политики
        // $this->authorize('create', Goods::class);
        // return $catalog_id;

        // Добавление связи
        $catalog = Catalog::with($product_type)->findOrFail($catalog_id);
        // return $catalog->count();

        if ($catalog->$product_type->find($product_id)) {

            return 'empty';

        } else {

            $catalog->$product_type()->attach($product_id, ['display'=>1]);

            // Вывод результата с изменением
            // Получаем из сессии необходимые данные (Функция находиться в Helpers)
            // $answer = operator_right($this->entity_name, false, 'index');

            // -------------------------------------------------------------------------------------------
            // ГЛАВНЫЙ ЗАПРОС
            // -------------------------------------------------------------------------------------------
            $catalog = Catalog::with([$product_type => function ($query) {
                $query->orderBy('catalog_products.sort', 'asc');
            }])
            ->findOrFail($catalog_id);

            return view('catalog_products.content-core', compact('catalog'));
        }
    }
}
