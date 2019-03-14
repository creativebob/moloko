<?php

namespace App\Http\Controllers;

// Модели
use App\Raw;
use App\Article;
use App\RawsCategory;
use App\Manufacturer;
use App\Metric;
use App\Entity;

// Валидация
use Illuminate\Http\Request;
use App\Http\Requests\RawRequest;

// Куки
use Illuminate\Support\Facades\Cookie;

// Транслитерация
use Transliterate;

// Трейты
use App\Http\Controllers\Traits\ArticleTrait;

class RawController extends Controller
{

    // Настройки сконтроллера
    public function __construct(Raw $raw)
    {
        $this->middleware('auth');
        $this->raw = $raw;
        $this->class = Raw::class;
        $this->model = 'App\Raw';
        $this->entity_alias = with(new $this->class)->getTable();
        $this->entity_dependence = false;
    }

    use ArticleTrait;

    public function index(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Включение контроля активного фильтра
        $filter_url = autoFilter($request, $this->entity_alias);
        if (($filter_url != null)&&($request->filter != 'active')) {
            Cookie::queue(Cookie::forget('filter_' . $this->entity_alias));
            return Redirect($filter_url);
        }

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));
        // dd($answer);

        // -------------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -------------------------------------------------------------------------------------------------------------

        $columns = [
            'id',
            'article_id',
            'raws_category_id',
            'author_id',
            'company_id'
        ];

        $raws = Raw::with([
            'author',
            'company',
            'article' => function ($q) {
                $q->with([
                    'group',
                    'photo'
                ]);
            },
            'category' => function ($q) {
                $q->select([
                    'id',
                    'name'
                ]);
            },
        ])
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->booklistFilter($request)
        ->filter($request, 'author_id')
        // ->filter($request, 'raws_category_id', 'article.product')
        // ->filter($request, 'raws_product_id', 'article')
        ->where('archive', false)
        ->select($columns)
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->paginate(30);
        // dd($raws);


        // -----------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ------------------------------------------------------------------------------
        // -----------------------------------------------------------------------------------------------------------

        $filter = setFilter($this->entity_alias, $request, [
            'author',               // Автор записи
            'raws_category',    // Категория услуги
            // 'raws_product',     // Группа услуги
            // 'date_interval',     // Дата обращения
            'booklist'              // Списки пользователя
        ]);


        // Инфо о странице
        $page_info = pageInfo($this->entity_alias);

        return view('raws.index', compact('raws', 'page_info', 'filter'));
    }

    public function create(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('raws_categories', false, 'index');

        // Главный запрос
        $raws_categories = RawsCategory::withCount('manufacturers')
        ->with('manufacturers')
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer)
        ->orderBy('sort', 'asc')
        ->get();

        if($raws_categories->count() == 0){

            // Описание ошибки
            $ajax_error = [];
            $ajax_error['title'] = "Обратите внимание!";
            $ajax_error['text'] = "Для начала необходимо создать категории сырья. А уже потом будем добавлять сырье. Ок?";
            $ajax_error['link'] = "/admin/raws_categories";
            $ajax_error['title_link'] = "Идем в раздел категорий";

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
            $ajax_error['text'] = "Для начала необходимо добавить производителей. А уже потом будем добавлять сырьё. Ок?";
            $ajax_error['link'] = "/admin/manufacturers/create"; // Ссылка на кнопке
            $ajax_error['title_link'] = "Идем в раздел производителей"; // Текст на кнопке

            return view('ajax_error', compact('ajax_error'));
        }

        // Если в категориях не добавлены производители
        // if ($raws_categories->where('manufacturers_count', 0)->count() == $raws_categories->count()){

        //     // Описание ошибки
        //     // $ajax_error = [];
        //     $ajax_error['title'] = "Обратите внимание!"; // Верхняя часть модалки
        //     $ajax_error['text'] = "Для начала необходимо добавить производителей в категории. А уже потом будем добавлять сырьё. Ок?";
        //     $ajax_error['link'] = "/admin/raws_categories"; // Ссылка на кнопке
        //     $ajax_error['title_link'] = "Идем в раздел категорий cырья"; // Текст на кнопке

        //     return view('ajax_error', compact('ajax_error'));
        // }

        // $raws_products_count = $raws_categories->first()->raws_products_count;

        // if ($request->cookie('conditions') != null) {

        //     $condition = Cookie::get('conditions');
        //     if(isset($condition['raws_category'])) {
        //         $raws_category_id = $condition['raws_category'];
        //         $raws_category = $raws_categories->find($raws_category_id);
        //         // dd($raws_category);
        //         $raws_products_count = $raws_category->raws_products_count;
        //         $parent_id = $raws_category_id;
        //         // dd($raws_products_count);
        //     }
        // }

        return view('includes.create_modes.create', [
            'item' => new $this->class,
            'title' => 'Добавление сырья',
            'entity' => $this->entity_alias,
            'categories_select_name' => 'raws_category_id',
            'category_entity_alias' => 'raws_categories',
            'set_status' => false
        ]);
    }

    public function store(RawRequest $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        $raws_category = RawsCategory::findOrFail($request->raws_category_id);
        // dd($goods_category->load('groups'));
        $article = $this->storeArticle($request, $raws_category);

        if ($article) {

            // Получаем данные для авторизованного пользователя
            $user = $request->user();

            $raw = new Raw;
            $raw->article_id = $article->id;
            $raw->raws_category_id = $request->raws_category_id;

            $raw->display = $request->display;
            $raw->system_item = $request->system_item;

            $raw->company_id = $user->company_id;
            $raw->author_id = hideGod($user);
            $raw->save();

            if ($raw) {

                // Пишем куки состояния
                // $mass = [
                //     'goods_category' => $goods_category_id,
                // ];
                // Cookie::queue('conditions_goods_category', $goods_category_id, 1440);

                // dd($request->quickly);
                if ($request->quickly == 1) {
                    return redirect()->route('raws.index');
                } else {
                    return redirect()->route('raws.edit', ['id' => $raw->id]);
                }
            } else {
                abort(403, 'Ошибка записи сырья');
            }
        } else {
            abort(403, 'Ошибка записи информации сырья');
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
        $raw = Raw::moderatorLimit($answer)
        ->findOrFail($id);
        // dd($raw);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $raw);

        // -- TODO -- Перенести в запрос --

        // // Массив со значениями метрик товара
        // if ($raw->article->metrics->isNotEmpty()) {
        //     // dd($raw->metrics);
        //     $metrics_values = [];
        //     foreach ($raw->article->metrics->groupBy('id') as $metric) {
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
        // // dd($metrics_values);

        $article = $raw->article;

        // Получаем настройки по умолчанию
        $settings = getSettings($this->entity_alias);

        // Инфо о странице
        $page_info = pageInfo($this->entity_alias);
        // dd($page_info);

        return view('raws.edit', compact('raw', 'article', 'page_info', 'settings'));
    }

    public function update(RawRequest $request, $id)
    {

        // dd($request);

        // Получаем из сессии необходимые данные (Функция находится в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $raw = Raw::with('article.product')->moderatorLimit($answer)->findOrFail($id);
        // dd($raw);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $raw);

        // Получаем артикул товара
        $raws_article = $raw->article;
        // dd($raw->raws_article->draft);
        //
        // Проверки только для черновика
        if ($raw->article->draft == 1) {

            // Определяем количество метрик и составов
            $metrics_count = isset($request->metrics) ? count($request->metrics) : 0;
            // dd($metrics_count);
            //
            $compositions_count = 0;

            $compositions_values = null;

            // Если пришли значения метрик
            $metrics_values = [];
            if (isset($request->metrics)) {
                // dd($request->metrics);

                // Получаем метрики, чтобы узнать их тип и знаки после запятой
                $keys = array_keys($request->metrics);
                // dd($keys);
                $metrics = Metric::with(['property' => function ($q) {
                    $q->select('id', 'type');
                }])
                ->select('id', 'decimal_place', 'property_id')
                ->findOrFail($keys)
                ->keyBy('id');
                // dd($metrics);

                // Приводим значения в соответкствие
                foreach ($request->metrics as $metric_id => $values) {
                    // dd($metrics[$metric_id]->decimal_place);
                    if (($metrics[$metric_id]->property->type == 'numeric') || ($metrics[$metric_id]->property->type == 'percent')) {
                        // dd(round($value[0] , $metrics[$metric_id]->decimal_place, PHP_ROUND_HALF_UP));
                        if ($metrics[$metric_id]->decimal_place != 0) {
                            $metrics_values[$metric_id][] = round($values[0] , $metrics[$metric_id]->decimal_place, PHP_ROUND_HALF_UP);
                        } else {
                            $metrics_values[$metric_id][] = (int)number_format($values[0], 0);
                        }
                    } else {
                        $metrics_values[$metric_id] = $values;
                    }
                }
                // dd($metrics_values);
            }

            // $compositions_count = isset($request->compositions_values) ? count($request->compositions_values) : 0;
            // dd($compositions_count);

            // Если пришли значения состава
            // $compositions_values = [];
            // if (isset($request->compositions_values)) {
            //     // dd($request->compositions_values);

            //     if ($raw->raws_article->raws_product->set_status == 'one') {
            //         // Приводим значения в соответкствие
            //         foreach ($request->compositions_values as $composition_id => $value) {
            //             $compositions_values[$composition_id] = round($value , 2, PHP_ROUND_HALF_UP);
            //         }
            //     } else {
            //         foreach ($request->compositions_values as $composition_id => $value) {
            //             $compositions_values[$composition_id] = (int)number_format($value, 0);
            //         }
            //     }
            // }
            // dd($compositions_values);

            // Производитель
            $manufacturer_id = isset($request->manufacturer_id) ? $request->manufacturer_id : null;

            // если в черновике поменяли производителя
            if ($raw->article->draft == 1) {
                if ($manufacturer_id != $raw->article->manufacturer_id) {
                    $raws_article = $raw->article;
                    $raws_article->manufacturer_id = $manufacturer_id;
                    $raws_article->save();
                }
            }

            if ($raws_article->name != $request->name) {
                $raws_article->name = $request->name;
            }

            $raws_article->manufacturer_id = $request->manufacturer_id;
            $raws_article->metrics_count = $metrics_count;
            $raws_article->compositions_count = 0;
            $raws_article->save();

            // Если нет прав на создание полноценной записи - запись отправляем на модерацию
            if ($answer['automoderate'] == false) {
                $raw->moderation = 1;
            }

            // Метрики
            if (count($metrics_values)) {

                $raws_article->metrics()->detach();

                $metrics_insert = [];
                // $metric->min = round($request->min , $request->decimal_place, PHP_ROUND_HALF_UP);
                foreach ($metrics_values as $metric_id => $values) {
                    foreach ($values as $value) {
                        // dd($value);
                        $raws_article->metrics()->attach([
                            $metric_id => [
                                'value' => $value,
                            ]
                        ]);
                    }
                }
                // dd($metrics_insert);
            } else {
                $raws_article->metrics()->detach();
            }

            // Состав
            // $compositions_relation = ($raws_article->raws_product->set_status == 'one') ? 'compositions' : 'set_compositions';
            // if (count($compositions_values)) {

            //     $raws_article->$compositions_relation()->detach();

            //     $compositions_insert = [];
            //     foreach ($compositions_values as $composition_id => $value) {
            //         $compositions_insert[$composition_id] = [
            //             'value' => $value,
            //         ];
            //     }
            //     // dd($compositions_insert);
            //     $raws_article->$compositions_relation()->attach($compositions_insert);
            // } else {
            //     $raws_article->$compositions_relation()->detach();
            // }
        }

        // Если снят флаг черновика, проверяем на совпадение артикула
        if (empty($request->draft) && $raw->article->draft == 1) {

            // dd($request);


            $check_name = $this->check_coincidence_name($request);

            // dd($check_name);
            if ($check_name) {
                return redirect()->back()->withInput()->withErrors('Такой артикул уже существует других в группах');
            }

            $check_article = $this->check_coincidence_article($metrics_count, $metrics_values, $compositions_count, $compositions_values, $request->raws_product_id, $manufacturer_id);

            if ($check_article) {

                return redirect()->back()->withInput()->withErrors('Такой артикул уже существует в группе!');
            }

            $raws_article = $raw->article;
            $raws_article->draft = null;
            $raws_article->save();



            // $raws_article = rawsArticle::where('id', $raw->raws_article_id)->update(['draft' => null]);
        }

        // Если проверки пройдены, или меняем уже товар

        // -------------------------------------------------------------------------------------------------
        // ПЕРЕНОС ГРУППЫ СЫРЬЯ В ДРУГУЮ КАТЕГОРИЮ ПОЛЬЗОВАТЕЛЕМ

        // Получаем выбранную категорию со страницы (то, что указал пользователь)
        $raws_category_id = $request->raws_category_id;

        // Смотрим: была ли она изменена
        if ($raw->article->product->raws_category_id != $raws_category_id) {

            // Была изменена! Переназначаем категорию группе:
            $item = RawsProduct::where('id', $raw->article->raws_product_id)
            ->update(['raws_category_id' => $raws_category_id]);
        }

        // -------------------------------------------------------------------------------------------------
        // ПЕРЕНОС СЫРЬЯ В ДРУГУЮ ГРУППУ ПОЛЬЗОВАТЕЛЕМ
        // Важно! Важно проверить, соответствеут ли группа в которую переноситься товар, метрикам самого товара
        // Если не соответствует - дать отказ. Если соответствует - осуществить перенос

        // Получаем выбранную группу со страницы (то, что указал пользователь)
        $raws_product_id = $request->raws_product_id;

        if ($raw->article->raws_product_id != $raws_product_id ) {

            // Была изменена! Переназначаем категорию группе:
            $item = RawsArticle::where('id', $raw->raws_article_id)
            ->update(['raws_product_id' => $raws_product_id]);
        }

        // А, пока изменяем без проверки

        // Порции
        $raw->portion_status = $request->portion_status;
        $raw->portion_name = $request->portion_name;
        $raw->portion_abbreviation = $request->portion_abbreviation;
        $raw->portion_count = $request->portion_count;


        // Описание
        $raw->description = $request->description;

        // Названия артикулов
        $raw->manually = $request->manually;
        $raw->external = $request->external;

        // Цены
        $raw->cost = $request->cost;
        $raw->price = $request->price;

        // Общие данные
        $raw->display = $request->display;
        $raw->system_item = $request->system_item;

        $raw->editor_id = hideGod($request->user());
        $raw->save();

        if ($raw) {

            // Cохраняем / обновляем фото
            savePhoto($request, $raw);

            // Проверяем каталоги
            if (isset($request->catalogs)) {

                $catalogs_insert = [];
                foreach ($request->catalogs as $catalog) {
                    $catalogs_insert[$catalog] = ['display' => 1];
                }
                // dd($catalogs_insert);
                $raw->catalogs()->sync($catalogs_insert);
            } else {
                $raw->catalogs()->detach();
            }

            if ($raws_article->name != $request->name) {
                // dd($request);
                $raws_article->name = $request->name;
                $raws_article->save();
            }

            // Если ли есть
            if ($request->cookie('backlink') != null) {
                $backlink = Cookie::get('backlink');
                return Redirect($backlink);
            }

            return redirect()->route('raws.index');
        } else {
            abort(403, 'Ошибка записи группы товаров');
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
        $raw = Raw::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod('destroy'), $raw);

        if ($raw) {

            // Получаем пользователя
            $user = $request->user();

            // Скрываем бога
            $user_id = hideGod($user);

            RawsArticle::where('id', $raw->raws_article_id)->update(['editor_id' => $user_id, 'archive' => 1]);

            $raw->editor_id = $user_id;
            $raw->save();

            if ($raw) {
                return redirect()->route('raws.index');
            } else {
                abort(403, 'Ошибка при архивации сырья');
            }
        } else {
            abort(403, 'Сырьё не найдено');
        }
    }

    // ----------------------------------- Ajax -----------------------------------------

    // Режим создания товара
    public function ajax_change_create_mode(Request $request)
    {
        // $mode = 'mode-add';
        // $entity = 'service_categories';

        switch ($request->mode) {

            case 'mode-default':

            return view('raws.create_modes.mode_default');

            break;

            case 'mode-select':

            $set_status = $request->set_status == 'true' ? 1 : 0;
            $raws_category = RawsCategory::with(['groups' => function ($q) use ($set_status) {
                $q->with('unit')
                ->where('set_status', $set_status);
            }])
            ->find($request->category_id);

            $articles_groups = $raws_category->groups;

            return view('raws.create_modes.mode_select', compact('articles_groups'));

            break;

            case 'mode-add':

            return view('raws.create_modes.mode_add');

            break;

        }
    }


    // -------------------------------------- Проверки на совпадение артикула ----------------------------------------------------

    // Проверка имени по компании
    public function check_coincidence_name($request)
    {

        // Смотрим имя артикула по системе
            // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_raws_articles = operator_right('raws_article', false, 'index');

        $raws_articles = RawsArticle::moderatorLimit($answer_raws_articles)
        ->companiesLimit($answer_raws_articles)
        ->whereNull('draft')
        ->whereNull('archive')
        ->whereName($request->name)
        ->get(['name', 'raws_product_id']);
        // dd($raws_articles);
        // dd($request);

        if (count($raws_articles)) {

            // Смотрим группу артикулов
            $diff_count = $raws_articles->where('raws_product_id', '!=', $request->raws_product_id)->count();
            // dd($diff_count);
            if ($diff_count > 0) {
                return true;
            }
        }
    }

    public function check_coincidence_article($metrics_count, $metrics_values, $compositions_count, $compositions_values, $raws_product_id, $manufacturer_id = null)
    {


        // Вытаскиваем артикулы продукции с нужным нам числом метрик и составов
        $raws_articles = RawsArticle::with('metrics', 'compositions', 'set_compositions')
        ->where('raws_product_id', $raws_product_id)
        ->where(['metrics_count' => $metrics_count, 'compositions_count' => $compositions_count])
        ->whereNull('draft')
        ->whereNull('archive')
        ->get();

        // dd($raws_articles);

        if ($raws_articles) {

            // Создаем массив совпадений
            $coincidence = [];
            // dd($request);

            // Сравниваем метрики
            foreach ($raws_articles as $raws_article) {
                // foreach ($raws_article->raws as $cur_raws) {
                // dd($raws_articles);

                // Формируем массив метрик артикула
                $metrics_array = [];
                foreach ($raws_article->metrics as $metric) {
                    // dd($metric);
                    $metrics_array[$metric->id][] = $metric->pivot->value;
                }

                // Если значения метрик совпали, создаюм ключ метрик
                if ($metrics_array == $metrics_values) {
                    $coincidence['metrics'] = 1;
                }

                // Формируем массив составов артикула
                $compositions_array = [];
                if ($raws_article->product->set_status == 'one') {
                    foreach ($raws_article->compositions as $composition) {
                        // dd($composition);
                        $compositions_array[$composition->id] = $composition->pivot->value;
                    }
                } else {
                    foreach ($raws_article->set_compositions as $composition) {
                        // dd($composition);
                        $compositions_array[$composition->id] = $composition->pivot->value;
                    }
                }

                if ($compositions_array == $compositions_values) {
                    // Если значения метрик совпали, создаюм ключ метрик
                    $coincidence['compositions'] = 1;
                }

                if ($raws_article->manufacturer_id == $manufacturer_id) {
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
