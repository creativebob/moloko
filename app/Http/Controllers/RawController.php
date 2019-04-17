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
use App\Http\Controllers\Traits\Articles\ArticleTrait;

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
            'set_status',
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

        return view('includes.tmc.create.create', [
            'item' => new $this->class,
            'title' => 'Добавление сырья',
            'entity' => $this->entity_alias,
            'categories_select_name' => 'raws_category_id',
            'category_entity_alias' => 'raws_categories',
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

            $raw->set_status = $request->has('set_status');

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
        // dd($article);

        // Получаем настройки по умолчанию
        $settings = getSettings($this->entity_alias);

        // Инфо о странице
        $page_info = pageInfo($this->entity_alias);
        // dd($page_info);

        return view('includes.tmc.edit.edit', [
            'title' => 'Редактировать сырье',
            'item' => $raw,
            'article' => $article,
            'page_info' => $page_info,
            'settings' => $settings,
            'entity' => $this->entity_alias,
            'category_entity' => 'raws_categories',
            'categories_select_name' => 'raws_category_id',
        ]);
    }

    public function update(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находится в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $raw = Raw::moderatorLimit($answer)
        ->findOrFail($id);
        // dd($raw);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $raw);


        $article = $this->updateArticle($request, $raw);

        if ($article) {

            // ПЕРЕНОС ГРУППЫ ТОВАРА В ДРУГУЮ КАТЕГОРИЮ ПОЛЬЗОВАТЕЛЕМ

            // dd($request);
            // Получаем выбранную категорию со страницы (то, что указал пользователь)
            $raws_category_id = $request->raws_category_id;

            // Смотрим: была ли она изменена
            if ($raw->raws_category_id != $raws_category_id) {

                $articles_group = $article->group;

                // Была изменена! Переназначаем категорию товару и группе:
                $articles_group->raws_categories()->detach($raw->goods_category_id);
                $raw->raws_category_id = $raws_category_id;

                $articles_group->raws_categories()->attach($raws_category_id);
                // $articles_group->goods_categories()->updateExistingPivot($article->articles_group_id, $goods_category);
            }

            $raw->display = $request->display;
            $raw->system_item = $request->system_item;
            $raw->save();


            // Если ли есть
            if ($request->cookie('backlink') != null) {
                $backlink = Cookie::get('backlink');
                return Redirect($backlink);
            }

            return redirect()->route('raws.index');
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
