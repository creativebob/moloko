<?php

namespace App\Http\Controllers;

// Модели
use App\GoodsStock;
use App\Goods;
use App\Article;
use App\GoodsCategory;
use App\Raw;
use App\RawsArticle;
use App\Manufacturer;
use App\Album;
use App\Metric;
use App\Entity;

use App\Catalog;

// Валидация
use Illuminate\Http\Request;
use App\Http\Requests\GoodsStoreRequest;
use App\Http\Requests\ArticleStoreRequest;

// Куки
use Illuminate\Support\Facades\Cookie;

// Транслитерация
use Illuminate\Support\Str;

// Трейты
use App\Http\Controllers\Traits\Articlable;

use Illuminate\Support\Facades\Log;

class StockGoodsController extends Controller
{

    // Настройки сконтроллера
    public function __construct(GoodsStock $stock_goods)
    {
        $this->middleware('auth');
        $this->stock_goods = $stock_goods;
        $this->class = GoodsStock::class;
        $this->model = 'App\GoodsStock';
        $this->entity_alias = with(new $this->class)->getTable();
        $this->entity_dependence = true;
    }

    use Articlable;

    public function index(Request $request)
    {

        // Подключение политики
        $this->authorize('index', $this->class);

        // Включение контроля активного фильтра
        $filter_url = autoFilter($request, $this->entity_alias);

        if (($filter_url != null)&&($request->filter != 'active')) {
            Cookie::queue(Cookie::forget('filter_' . $this->entity_alias));
            return Redirect($filter_url);
        }

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // -----------------------------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -----------------------------------------------------------------------------------------------------------------------------

        $columns = [
            'id',
            'stock_id',
            'goods_id',
            'company_id',
            'count',
            'serial',
            'manufacturer_id',
        ];

        $stock_goods = GoodsStock::with([
            'company',
            'goods',
            'manufacturer'
        ])
        ->companiesLimit($answer)
        ->filials($answer)
        ->booklistFilter($request)

        ->whereHas('goods', function($q) use ($request){
            $q->filter($request, 'category_id');
        })

        ->select($columns)
        // ->orderBy('id', 'desc')
        ->paginate(30);
        // dd($goods);

        // -----------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ------------------------------------------------------------------------------
        // -----------------------------------------------------------------------------------------------------------

        $filter = setFilter($this->entity_alias, $request, [
            'goods_category',           // Категория товара
            'booklist'                  // Списки пользователя
        ]);

        // Окончание фильтра -----------------------------------------------------------------------------------------

        // Инфо о странице
        $page_info = pageInfo($this->entity_alias);

        return view('stock_products.common.index.index', [
            'items' => $stock_goods,
            'page_info' => $page_info,
            'class' => $this->class,
            'entity' => $this->entity_alias,
            'filter' => $filter,
        ]);
    }

    public function search($text_fragment)
    {

        // Подключение политики
        $this->authorize('index', Goods::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // --------------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // --------------------------------------------------------------------------------------------------------------

        $result_search = Goods::with('author', 'company', 'article.product.category')
        ->companiesLimit($answer)
        ->where('name', 'LIKE', '%'.$text_fragment.'%')
        ->whereNull('archive')
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->get();

        if ($result_search->count()) {
            
            $entity_alias = $this->entity_alias;
            return view('includes.search', compact('result_search', 'entity_alias'));
        } else {

            return view('includes.search');
        }
    }

    public function create(Request $request)
    {

    }

    public function store(ArticleStoreRequest $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        Log::channel('operations')
        ->info('============================== НАЧИНАЕМ ЗАПИСЬ ТОВАРА ==============================');

        $goods_category = GoodsCategory::findOrFail($request->category_id);
        // dd($goods_category->load('groups'));
        $article = $this->storeArticle($request, $goods_category);

        if ($article) {

            $data = $request->input();

            $data['article_id'] = $article->id;
            $data['price_unit_category_id'] = $data['units_category_id'];
            $data['price_unit_id'] = $data['unit_id'];

            $cur_goods = (new Goods())->create($data);

            if ($cur_goods) {

                $goods_category = $goods_category->load('metrics:id', 'raws:id');

                $metrics = $goods_category->metrics->pluck('id')->toArray();
                $cur_goods->metrics()->sync($metrics);

                $raws = $goods_category->raws->pluck('id')->toArray();
                $article->raws()->sync($raws);

                // Пишем куки состояния
                // $mass = [
                //     'goods_category' => $goods_category_id,
                // ];
                // Cookie::queue('conditions_goods_category', $goods_category_id, 1440);

                Log::channel('operations')
                ->info('Записали товар с id: ' . $cur_goods->id);
                Log::channel('operations')
                ->info('Автор: ' . $cur_goods->author->name . ' id: ' . $cur_goods->author_id .  ', компания: ' . $cur_goods->company->name . ', id: ' .$cur_goods->company_id);
                Log::channel('operations')
                ->info('========================================== КОНЕЦ ЗАПИСИ ТОВАРА ==============================================

                    ');

                // dd($request->quickly);
                if ($request->quickly == 1) {
                    return redirect()->route('goods.index');
                } else {
                    return redirect()->route('goods.edit', ['id' => $cur_goods->id]);
                }
            } else {
                abort(403, 'Ошибка записи товара');
            }
        } else {
            abort(403, 'Ошибка записи информации товара');
        }
    }

    public function show($id)
    {
        //
    }

    public function edit(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // Главный запрос
        $cur_goods = Goods::moderatorLimit($answer)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $cur_goods);

        $cur_goods->load([
            'metrics.values',
            'metrics.property',
            'metrics.unit',
            'prices'
        ]);

        $article = $cur_goods->article->load([
            'raws.article.group.unit',
            'raws.category'
        ]);

        $dropzone = getSettings($this->entity_alias);
//      dd($settings);

        $dropzone['id'] = $article->id;
        $dropzone['entity'] = $article->getTable();

        // Инфо о странице
        $page_info = pageInfo($this->entity_alias);

        return view('products.articles.common.edit.edit', [
            'title' => 'Редактировать товар',
            'item' => $cur_goods,
            'article' => $article,
            'page_info' => $page_info,
            'dropzone' => json_encode($dropzone),
            'entity' => $this->entity_alias,
            'category_entity' => 'goods_categories',
            'categories_select_name' => 'goods_category_id',
        ]);
    }

    public function update(ArticleStoreRequest $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находится в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $cur_goods = Goods::moderatorLimit($answer)
        ->findOrFail($id);
        // dd($cur_goods);

        $article = $cur_goods->article;
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $cur_goods);

        $result = $this->updateArticle($request, $cur_goods);
        // Если результат не массив с ошибками, значит все прошло удачно
        if (!is_array($result)) {

            $cur_goods->display = $request->display;
            $cur_goods->system = $request->system;
            $cur_goods->price_unit_id = $request->price_unit_id;
            $cur_goods->price_unit_category_id = $request->price_unit_category_id;

            $cur_goods->save();

            // ПЕРЕНОС ГРУППЫ ТОВАРА В ДРУГУЮ КАТЕГОРИЮ ПОЛЬЗОВАТЕЛЕМ
            $this->changeCategory($request, $cur_goods);

            // Каталоги
            $data = [];
            if (isset($request->catalogs_items)) {

                foreach ($request->catalogs_items as $catalog_id => $items) {
                    foreach ($items as $item_id) {
                        $data[(int) $item_id] = [
                            'catalogs_goods_id' => $catalog_id,
                        ];
                    }
                }
            }
            // dd($data);
//            $cur_goods->catalogs_items()->sync($data);

            // Метрики
            if ($request->has('metrics')) {
                // dd($request);


                $metrics_insert = [];
                foreach ($request->metrics as $metric_id => $value) {
                    if (is_array($value)) {
                        $metrics_insert[$metric_id]['value'] = implode(',', $value);
                    } else {
                        $metrics_insert[$metric_id]['value'] = $value;
                    }
                }
                $cur_goods->metrics()->sync($metrics_insert);
            }

            // Если ли есть
            if ($request->cookie('backlink') != null) {
                $backlink = Cookie::get('backlink');
                return Redirect($backlink);
            }

            return redirect()->route('goods.index');
        } else {
            return back()
            ->withErrors($result)
            ->withInput();
        }
    }

    public function destroy($id)
    {
        //
    }

    public function archive(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, 'delete');

        // ГЛАВНЫЙ ЗАПРОС:
        $cur_goods = Goods::moderatorLimit($answer)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize('delete', $cur_goods);

        if ($cur_goods) {

            $cur_goods->archive = true;

            $cur_goods->editor_id = hideGod($request->user());
            $cur_goods->save();

            if ($cur_goods) {
                return redirect()->route('goods.index');
            } else {
                abort(403, 'Ошибка при архивации товара');
            }
        } else {
            abort(403, 'Товар не найден');
        }
    }

    // --------------------------------------------- Ajax -------------------------------------------------

    public function ajax_get_goods(Request $request)
    {
        $cur_goods = Goods::with([
            'article.group.unit',
            'category'
        ])
            ->find($request->id);

        return view('products.articles.goods.goods.goods_input', compact('cur_goods'));
    }

}
