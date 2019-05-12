<?php

namespace App\Http\Controllers;

// Модели
use App\Goods;
use App\Article;
use App\GoodsCategory;
use App\RawsArticle;
use App\Manufacturer;
use App\Album;
use App\Metric;
use App\Entity;

use App\Catalog;

// Валидация
use Illuminate\Http\Request;
use App\Http\Requests\GoodsRequest;
use App\Http\Requests\ArticleRequest;

// Куки
use Illuminate\Support\Facades\Cookie;

// Транслитерация
use Illuminate\Support\Str;

// Трейты
use App\Http\Controllers\Traits\Tmc\ArticleTrait;

class GoodsController extends Controller
{

    // Настройки сконтроллера
    public function __construct(Goods $cur_goods)
    {
        $this->middleware('auth');
        $this->cur_goods = $cur_goods;
        $this->class = Goods::class;
        $this->model = 'App\Goods';
        $this->entity_alias = with(new $this->class)->getTable();
        $this->entity_dependence = false;
    }

    use ArticleTrait;

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
            'article_id',
            'category_id',
            'set_status',
            'author_id',
            'company_id',
            'display',
            'system_item'
        ];

        $goods = Goods::with([
            'author',
            'company',
            'article' => function ($q) {
                $q->with([
                    'group',
                    'photo'
                ]);
                // ->select([
                //     'id',
                //     'name',
                //     'articles_group_id',
                //     'photo_id',
                //     'company_id'
                // ]);
            },
            'category' => function ($q) {
                $q->select([
                    'id',
                    'name'
                ]);
            },
            // 'catalogs.site'
        ])
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer)
        ->booklistFilter($request)
        ->filter($request, 'author_id')
        // ->filter($request, 'goods_category_id', 'article.product')
        // ->filter($request, 'goods_product_id', 'article')
        ->where('archive', false)
        ->select($columns)
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->paginate(30);
        // dd($goods);

        // -----------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ------------------------------------------------------------------------------
        // -----------------------------------------------------------------------------------------------------------

        $filter = setFilter($this->entity_alias, $request, [
            'author',               // Автор записи
            'goods_category',       // Категория товара
            // 'goods_product',     // Группа продукта
            // 'date_interval',     // Дата обращения
            'booklist'              // Списки пользователя
        ]);

        // Окончание фильтра -----------------------------------------------------------------------------------------

        // Инфо о странице
        $page_info = pageInfo($this->entity_alias);

        return view('goods.index', compact('goods', 'page_info', 'filter'));
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
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
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

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('goods_categories', false, 'index');

        // Главный запрос
        $goods_categories = GoodsCategory::withCount('manufacturers')
        ->with('manufacturers')
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer)
        ->orderBy('sort', 'asc')
        ->get();
        // dd($goods_categories->where('manufacturers_count', 0)->count());

        if ($goods_categories->count() == 0){

            // Описание ошибки
            $ajax_error = [];
            $ajax_error['title'] = "Обратите внимание!"; // Верхняя часть модалки
            $ajax_error['text'] = "Для начала необходимо создать категории товаров. А уже потом будем добавлять товары. Ок?";
            $ajax_error['link'] = "/admin/goods_categories"; // Ссылка на кнопке
            $ajax_error['title_link'] = "Идем в раздел категорий"; // Текст на кнопке

            return view('ajax_error', compact('ajax_error'));
        }

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('manufacturers', false, 'index');

        $manufacturers_count = Manufacturer::moderatorLimit($answer)
        ->companiesLimit($answer)
        ->systemItem($answer)
        ->count();

        // Если нет производителей
        if ($manufacturers_count == 0){

            // Описание ошибки
            // $ajax_error = [];
            $ajax_error['title'] = "Обратите внимание!"; // Верхняя часть модалки
            $ajax_error['text'] = "Для начала необходимо добавить производителей. А уже потом будем добавлять товары. Ок?";
            $ajax_error['link'] = "/admin/manufacturers/create"; // Ссылка на кнопке
            $ajax_error['title_link'] = "Идем в раздел производителей"; // Текст на кнопке

            return view('ajax_error', compact('ajax_error'));
        }

        // Если в категориях не добавлены производители
        // if ($goods_categories->where('manufacturers_count', 0)->count() == $goods_categories->count()){

        //     // Описание ошибки
        //     // $ajax_error = [];
        //     $ajax_error['title'] = "Обратите внимание!"; // Верхняя часть модалки
        //     $ajax_error['text'] = "Для начала необходимо добавить производителей в категории. А уже потом будем добавлять товары. Ок?";
        //     $ajax_error['link'] = "/admin/goods_categories"; // Ссылка на кнопке
        //     $ajax_error['title_link'] = "Идем в раздел категорий товаров"; // Текст на кнопке

        //     return view('ajax_error', compact('ajax_error'));
        // }

        $parent_id = null;

        // if ($request->cookie('conditions') != null) {

        //     $condition = Cookie::get('conditions');
        //     if(isset($condition['goods_category'])) {
        //         $goods_category_id = $condition['goods_category'];

        //         $goods_category = $goods_categories->find($goods_category_id);
        //         // dd($goods_category);

        //         $goods_products_count = $goods_category->goods_products_count;
        //         $parent_id = $goods_category_id;
        //         // dd($goods_products_count);
        //     }
        // }

        // Пишем в куку страницу на которой находимся
        // $backlink = url()->previous();
        // Cookie::queue('backlink', $backlink, 1440);

        return view('tmc.create.create', [
            'item' => new $this->class,
            'title' => 'Добавление товара',
            'entity' => $this->entity_alias,
            'category_entity' => 'goods_categories',
        ]);
    }

    public function store(ArticleRequest $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        $goods_category = GoodsCategory::findOrFail($request->category_id);
        // dd($goods_category->load('groups'));
        $article = $this->storeArticle($request, $goods_category);

        if ($article) {

            // Получаем данные для авторизованного пользователя
            $user = $request->user();

            $cur_goods = new Goods;
            $cur_goods->article_id = $article->id;
            $cur_goods->category_id = $request->category_id;

            $cur_goods->display = $request->display;
            $cur_goods->system_item = $request->system_item;

            $cur_goods->set_status = $request->has('set_status');

            $cur_goods->company_id = $user->company_id;
            $cur_goods->author_id = hideGod($user);
            $cur_goods->save();

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
            'metrics.unit'
        ]);

        $article = $cur_goods->article->load([
            'raws.article.group.unit',
            'raws.category'
        ]);

        $settings = getSettings($this->entity_alias);

        // Инфо о странице
        $page_info = pageInfo($this->entity_alias);

        return view('tmc.edit.edit', [
            'title' => 'Редактировать товар',
            'item' => $cur_goods,
            'article' => $article,
            'page_info' => $page_info,
            'settings' => $settings,
            'entity' => $this->entity_alias,
            'category_entity' => 'goods_categories',
            'categories_select_name' => 'goods_category_id',
        ]);
    }

    public function update(ArticleRequest $request, $id)
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
            $cur_goods->system_item = $request->system_item;
            $cur_goods->save();

            // ПЕРЕНОС ГРУППЫ ТОВАРА В ДРУГУЮ КАТЕГОРИЮ ПОЛЬЗОВАТЕЛЕМ
            $this->changeCategory($request, $cur_goods);

            // Каталоги
            $cur_goods->catalogs_items()->sync($request->catalogs_items);

            // Метрики
            if ($request->has('metrics')) {
                // dd($request);

                $cur_goods->metrics()->detach();

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

    // ----------------------------------- Ajax -----------------------------------------

    // Отображение на сайте
    public function ajax_sync(Request $request)
    {

        // Описание ошибки
        $ajax_error = [];
        $ajax_error['title'] = "Обратите внимание!"; // Верхняя часть модалки
        $ajax_error['text'] = "Для начала необходимо создать категории товаров. А уже потом будем добавлять товары. Ок?";
        $ajax_error['link'] = "/admin/goods_categories"; // Ссылка на кнопке
        $ajax_error['title_link'] = "Идем в раздел категорий"; // Текст на кнопке

        return view('ajax_error', compact('ajax_error'));
    }

    // Проверка совпадения артикула
    public function ajax_check(Request $request)
    {

        $goods_count = Goods::where(['manually' => $request->value, 'company_id' => $request->user()->company_id])
        ->where('id', '!=', $request->id)
        ->count();

        return response()->json($goods_count);
    }

    // Для заказа
    public function ajax_get_products(Request $request)
    {

        $id = $request->id;
        // $id = 3;

        $goods_list = Goods::with('article', 'photo')
        ->whereHas('article', function ($query) use ($id) {
            $query->whereNull('draft')
            ->whereNull('archive')
            ->whereHas('product', function ($query) use ($id) {
                $query->whereHas('category', function ($query) use ($id) {
                    $query->where('id', $id);
                });
            });
        })
        ->get();
        // dd($goods_list);

        return view('leads.items_for_category', [
            'items_list' => $goods_list,
            'entity' => $this->entity_alias
        ]);
    }

}
