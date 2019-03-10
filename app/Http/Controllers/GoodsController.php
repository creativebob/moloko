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

// Куки
use Illuminate\Support\Facades\Cookie;

// Транслитерация
use Transliterate;

// Трейты
use App\Http\Controllers\Traits\ArticleTrait;

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
            'goods_category_id',
            'author_id',
            'company_id'
        ];

        $goods = Goods::with([
            'author',
            'company',
            'article' => function ($q) {
                $q->with('group')
                ->select([
                    'id',
                    'name'
                ]);
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
        ->systemItem($answer)
        ->where('company_id', $request->user()->company_id)
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

        // Функция отрисовки списка со вложенностью и выбранным родителем (Отдаем: МАССИВ записей, Id родителя записи, параметр блокировки категорий (1 или null), запрет на отображенеи самого элемента в списке (его Id))
        $goods_categories_list = get_select_tree($goods_categories->keyBy('id')->toArray(), $parent_id, null, null);
        // echo $goods_categories_list;

        return view('goods.create', [
            'cur_goods' => new $this->class,
            'goods_categories_list' => $goods_categories_list,
        ]);
    }

    public function store(GoodsRequest $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        $article = $this->storeArticle($request);

        if ($article) {

            // Получаем данные для авторизованного пользователя
            $user = $request->user();

            $cur_goods = new Goods;
            $cur_goods->article_id = $article->id;
            $cur_goods->goods_category_id = $request->goods_category_id;
            $cur_goods->company_id = $user->company_id;
            $cur_goods->author_id = hideGod($user);
            $cur_goods->save();

            if ($cur_goods) {

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
        $cur_goods = Goods::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $cur_goods);

        // Главный запрос
        // if ($cur_goods->goods_article->goods_product->set_status == 'one') {

        //     if ($cur_goods->goods_article->draft == 1) {
        //         $cur_goods->load([
        //             'goods_article' => function ($q) {
        //                 $q->with([
        //                     'metrics',
        //                     'compositions.raws_product' => function ($q) {
        //                         $q->with('unit', 'raws_category');
        //                     },
        //                     'goods_product.goods_category' => function ($query) {
        //                         $query->with([
        //                             'one_metrics' => function ($q) {
        //                                 $q->with(['property', 'values']);
        //                             },
        //                             'compositions.raws_product.unit'
        //                         ]);
        //                     },
        //                 ])
        //                 ->withCount(['metrics', 'compositions']);
        //             },
        //             'album.photos',
        //             'company.manufacturers'
        //         ]);
        //     } else {
        //         $cur_goods->load([
        //             'goods_article' => function ($q) {
        //                 $q->with([
        //                     'metrics',
        //                     'compositions.raws_product' => function ($q) {
        //                         $q->with('unit', 'raws_category');
        //                     },
        //                     'goods_product.goods_category'
        //                 ])
        //                 ->withCount(['metrics', 'compositions']);
        //             },
        //             'album.photos'
        //         ]);
        //     }
        // } else {
        //     if ($cur_goods->goods_article->draft == 1) {
        //         $cur_goods->load([
        //             'goods_article' => function ($q) {
        //                 $q->with([
        //                     'metrics',
        //                     'set_compositions.goods_product' => function ($q) {
        //                         $q->with('unit');
        //                     },
        //                     'goods_product.goods_category' => function ($query) {
        //                         $query->with([
        //                             'set_metrics' => function ($q) {
        //                                 $q->with(['property', 'values']);
        //                             }
        //                         ]);
        //                     },
        //                 ])
        //                 ->withCount(['metrics', 'set_compositions']);
        //             },
        //             'album.photos',
        //             'company.manufacturers'
        //         ]);
        //     } else {
        //         $cur_goods->load([
        //             'goods_article' => function ($q) {
        //                 $q->with([
        //                     'metrics',
        //                     'set_compositions.raws_product' => function ($q) {
        //                         $q->with('unit', 'raws_category');
        //                     },
        //                     'goods_product.goods_category'
        //                 ])
        //                 ->withCount(['metrics', 'set_compositions']);
        //             },
        //             'album.photos'
        //         ]);
        //     }
        // }
        // dd($cur_goods);

        // -- TODO -- Перенести в запрос --

        // Массив со значениями метрик товара
        // if (count($cur_goods->article->metrics)) {
        //     // dd($cur_goods->metrics);
        //     $metrics_values = [];
        //     foreach ($cur_goods->article->metrics->groupBy('id') as $metric) {
        //         // dd($metric);
        //         if ((count($metric) == 1) && ($metric->first()->list_type != 'list')) {
        //             $metrics_values[$metric->first()->id] = $metric->first()->pivot->value;
        //         } else {
        //             foreach ($metric as $value) {
        //                 $metrics_values[$metric->first()->id][] = $value->pivot->value;
        //             }
        //         }
        //     }
        // } else {
        //     $metrics_values = null;
        // }
        // dd($metrics_values);
        //
        // Если товар в статусе черновика
        // if ($cur_goods->article->draft == 1) {

        //     // Формируем списки составов
        //     // Статус товара "один"
        //     if ($cur_goods->article->product->set_status == 'one') {

        //         // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        //         $answer_raws_categories = operator_right('raws_categories', false, 'index');
        //         $answer_raws_products = operator_right('raws_products', false, 'index');
        //         $answer_raws = operator_right('raws', false, 'index');

        //         $raws_articles = RawsArticle::with(['product' => function ($q) {
        //             $q->with(['category' => function ($q) {
        //                 $q->select('id', 'name');
        //             }])->select('id', 'name', 'raws_category_id');
        //         }])
        //         ->select('id', 'name', 'raws_product_id')
        //         ->whereHas('raws', function ($query) {
        //             $query->whereNull('draft');
        //         })
        //         ->moderatorLimit($answer_raws_categories)
        //         ->companiesLimit($answer_raws_categories)
        //         ->authors($answer_raws_categories)
        //         ->systemItem($answer_raws_categories)
        //         ->get()
        //         ->keyBy('id')
        //         ->groupBy('product.category.name');

        //         $composition_list = [
        //             'name' => 'Сырье',
        //             'alias' => 'raws',
        //             'composition_categories' => $raws_articles,
        //         ];
        //     } else {

        //         // Статус товара "набор"
        //         // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        //         $answer_goods_categories = operator_right('goods_categories', false, 'index');
        //         $answer_goods_products = operator_right('goods_products', false, 'index');
        //         $answer_goods = operator_right('goods', false, 'index');

        //         $goods_articles = GoodsArticle::with(['product' => function ($q) {
        //             $q->with(['category' => function ($q) {
        //                 $q->select('id', 'name');
        //             }])->select('id', 'name', 'goods_category_id');
        //         }])
        //         ->select('id', 'name', 'goods_product_id')
        //         ->whereHas('goods', function ($query) {
        //             $query->whereNull('draft');
        //         })
        //         ->moderatorLimit($answer_goods_categories)
        //         ->companiesLimit($answer_goods_categories)
        //         ->authors($answer_goods_categories)
        //         ->systemItem($answer_goods_categories)
        //         ->get()
        //         ->keyBy('id')
        //         ->groupBy('product.category.name');

        //         $composition_list = [
        //             'name' => 'Товары',
        //             'alias' => 'goods',
        //             'composition_categories' => $goods_articles,
        //         ];
        //     }
        //     // dd($composition_list);
        // }

        $article = $cur_goods->article;

        $settings = getSettings($this->entity_alias);
        // dd($settings);

        // Инфо о странице
        $page_info = pageInfo($this->entity_alias);
        // dd($page_info);

        return view('goods.edit', compact('cur_goods', 'article', 'page_info', 'settings'));
    }

    public function update(Request $request, $id)
    {

        // dd($request->catalogs_items);

        // Получаем из сессии необходимые данные (Функция находится в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $cur_goods = Goods::moderatorLimit($answer)
        ->findOrFail($id);
        // dd($cur_goods);



        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $cur_goods);

        $article = $this->updateArticle($request, $cur_goods);

        if ($article) {

            // Cохраняем / обновляем фото
            savePhoto($request, $article);

            // Проверяем каталоги
            if (isset($request->catalogs_items)) {

                // $catalogs_insert = [];
                // foreach ($request->catalogs as $catalog) {
                //     $catalogs_insert[$catalog] = ['display' => 1];
                // }
                // dd($catalogs_insert);
                $cur_goods->catalogs_items()->sync($request->catalogs_items);
            } else {
                $cur_goods->catalogs_items()->detach();
            }

            // Если ли есть
            if ($request->cookie('backlink') != null) {
                $backlink = Cookie::get('backlink');
                return Redirect($backlink);
            }

            return redirect()->route('goods.index');
        } else {
            abort(403, 'Ошибка обновления товара');
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

            $cur_goods->editor_id = hideGod($request->user());
            $cur_goods->archive = true;
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

    // Режим создания товара
    public function ajax_change_create_mode(Request $request)
    {
        $mode = $request->mode;
        $goods_category_id = $request->goods_category_id;
        // $mode = 'mode-add';
        // $entity = 'service_categories';

        switch ($mode) {

            case 'mode-default':

            return view('goods.create_modes.mode_default');

            break;

            // case 'mode-select':

            // $goods_products = GoodsProduct::with('unit')
            // ->where([
            //     'goods_category_id' => $goods_category_id,
            //     'set_status' => $request->set_status
            // ])
            // ->get(['id', 'name', 'unit_id']);
            // return view('goods.create_modes.mode_select', compact('goods_products'));

            // break;

            case 'mode-add':

            return view('goods.create_modes.mode_add');

            break;

        }
    }
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

    // -------------------------------------- Проверки на совпаденеи артикула ----------------------------------------------------

    // Проверка имени по компании
    public function check_coincidence_name($request)
    {

        // Смотрим имя артикула по системе
            // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_goods_articles = operator_right('goods_article', false, 'index');

        $goods_articles = GoodsArticle::moderatorLimit($answer_goods_articles)
        ->companiesLimit($answer_goods_articles)
        ->whereNull('draft')
        ->whereNull('archive')
        ->whereName($request->name)
        ->get(['name', 'goods_product_id']);
        // dd($goods_articles);
        // dd($request);

        if (count($goods_articles)) {

            // Смотрим группу артикулов
            $diff_count = $goods_articles->where('goods_product_id', '!=', $request->goods_product_id)->count();
            // dd($diff_count);
            if ($diff_count > 0) {
                return true;
            }
        }
    }

    public function check_coincidence_article($metrics_count, $metrics_values, $compositions_count, $compositions_values, $goods_product_id, $manufacturer_id = null)
    {

        // Вытаскиваем артикулы продукции с нужным нам числом метрик и составов
        $goods_articles = GoodsArticle::with('metrics', 'compositions', 'set_compositions')
        ->where('goods_product_id', $goods_product_id)
        ->where(['metrics_count' => $metrics_count, 'compositions_count' => $compositions_count])
        ->whereNull('draft')
        ->whereNull('archive')
        ->get();
        // dd($goods_articles);

        if ($goods_articles) {

            // Создаем массив совпадений
            $coincidence = [];
            // dd($request);

            // Сравниваем метрики
            foreach ($goods_articles as $goods_article) {
                // foreach ($goods_article->goods as $cur_goods) {
                // dd($goods_articles);

                // Формируем массив метрик артикула
                $metrics_array = [];
                foreach ($goods_article->metrics as $metric) {
                    // dd($metric);
                    $metrics_array[$metric->id][] = $metric->pivot->value;
                }

                // Если значения метрик совпали, создаюм ключ метрик
                if ($metrics_array == $metrics_values) {
                    $coincidence['metrics'] = 1;
                }

                // Формируем массив составов артикула
                $compositions_array = [];
                if ($goods_article->product->set_status == 'one') {
                    foreach ($goods_article->compositions as $composition) {
                        // dd($composition);
                        $compositions_array[$composition->id] = $composition->pivot->value;
                    }
                } else {
                    foreach ($goods_article->set_compositions as $composition) {
                        // dd($composition);
                        $compositions_array[$composition->id] = $composition->pivot->value;
                    }
                }

                if ($compositions_array == $compositions_values) {
                    // Если значения метрик совпали, создаюм ключ метрик
                    $coincidence['compositions'] = 1;
                }

                if ($goods_article->manufacturer_id == $manufacturer_id) {
                    // Если значения метрик совпали, создаюм ключ метрик
                    $coincidence['manufacturer'] = 1;
                }
                // }
            }
            // dd($coincidence);
            // Если ключи присутствуют, даем ошибку
            if (isset($coincidence['metrics']) && isset($coincidence['compositions']) && isset($coincidence['manufacturer'])) {

                // dd('ошибка');
                return true;
                // dd('lol');
            }
        }
        // dd($coincidence);
    }

}
