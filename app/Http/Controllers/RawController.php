<?php

namespace App\Http\Controllers;

use App\Http\Requests\RawStoreRequest;
use App\Http\Requests\RawUpdateRequest;
use App\Raw;
use App\RawsCategory;
use App\Manufacturer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Traits\Articlable;

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

    use Articlable;

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
            'category_id',
            'price_unit_id',
            'price_unit_category_id',

            'portion_status',
            'portion_name',
            'portion_abbreviation',
            'unit_portion_id',
            'portion_count',   

            'author_id',
            'company_id',
            'display',
            'system',
            'unit_for_composition_id'
        ];


        $raws = Raw::with([
            'author',
            'company',
            'compositions.goods',
            'unit_portion',
            'article' => function ($q) {
                $q->with([
                    'group',
                    'photo',
                    'unit',
                    'unit_weight',
                    'unit_volume'
                ]);
            },
            'category'
//            => function ($q) {
//                $q->select([
//                    'id',
//                    'name'
//                ]);
//            }
            ,
        ])
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->booklistFilter($request)
        ->filter($request, 'author_id')

        ->whereHas('article', function($q) use ($request){
            $q->filter($request, 'articles_group_id');
        })

        ->filter($request, 'category_id')

        ->where('archive', false)
//        ->select($columns)
        ->orderBy('moderation', 'desc')
        ->orderBy('id', 'desc')
        ->paginate(30);
        // dd($raws);

        // -----------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ------------------------------------------------------------------------------
        // -----------------------------------------------------------------------------------------------------------

        $filter = setFilter($this->entity_alias, $request, [
            'author',               // Автор записи
            'raws_category',        // Категория услуги
            'articles_group',       // Группа артикула
            'booklist'              // Списки пользователя
        ]);

        // Инфо о странице
        $page_info = pageInfo($this->entity_alias);

        return view('products.articles.common.index.index', [
            'items' => $raws,
            'page_info' => $page_info,
            'class' => $this->class,
            'entity' => $this->entity_alias,
            'category_entity' => 'raws_categories',
            'filter' => $filter,
        ]);
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

        return view('products.articles.common.create.create', [
            'item' => new $this->class,
            'title' => 'Добавление сырья',
            'entity' => $this->entity_alias,
            'category_entity' => 'raws_categories',
            'units_category_default' => 2,
            'unit_default' => 8,
        ]);
    }

    public function store(RawStoreRequest $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        Log::channel('operations')
        ->info('========================================== НАЧИНАЕМ ЗАПИСЬ СЫРЬЯ ==============================================');

        $raws_category = RawsCategory::findOrFail($request->category_id);
        // dd($goods_category->load('groups'));
        $article = $this->storeArticle($request, $raws_category);

        if ($article) {

            $data = $request->input();
            $data['article_id'] = $article->id;
            $data['price_unit_category_id'] = $data['units_category_id'];
            $data['price_unit_id'] = $data['unit_id'];

            $raw = (new Raw())->create($data);
            
            if ($raw) {

                // Пишем куки состояния
                // $mass = [
                //     'goods_category' => $goods_category_id,
                // ];
                // Cookie::queue('conditions_goods_category', $goods_category_id, 1440);

                Log::channel('operations')
                ->info('Записали сырье c id: ' . $raw->id);
                Log::channel('operations')
                ->info('Автор: ' . $raw->author->name . ' id: ' . $raw->author_id .  ', компания: ' . $raw->company->name . ', id: ' .$raw->company_id);
                Log::channel('operations')
                ->info('========================================== КОНЕЦ ЗАПИСИ СЫРЬЯ ==============================================

                    ');

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
        // dd($raw->coster);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $raw);

        $raw->load([
            'article' => function ($q) {
                $q->with([
                    'unit'
                ]);
            },
            'category' => function ($q) {
                $q->with([
                    'metrics'
                ]);
            },
        ]);
        $article = $raw->article;
        // dd($article);

        // Получаем настройки по умолчанию
//        $dropzone = getSettings($this->entity_alias);
//        $dropzone['id'] = $article->id;
//        $dropzone['entity'] = $article->getTable();
//        dd($dropzone);

        // Получаем настройки по умолчанию
        $settings = getSettings($this->entity_alias);

        // Инфо о странице
        $page_info = pageInfo($this->entity_alias);
        // dd($page_info);

        return view('products.articles.common.edit.edit', [
            'title' => 'Редактировать сырье',
            'item' => $raw,
            'article' => $article,
            'page_info' => $page_info,
            'settings' => $settings,
//            'dropzone' => json_encode($dropzone),
            'entity' => $this->entity_alias,
            'category_entity' => 'raws_categories',
            'categories_select_name' => 'raws_category_id',
            'raw' => $raw,
        ]);
    }

    public function update(RawUpdateRequest $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находится в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $raw = Raw::with('article')
        ->moderatorLimit($answer)
        ->findOrFail($id);
        // dd($raw);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $raw);

        $article = $raw->article;

        if ($article->draft) {
            $raw->unit_for_composition_id = $request->unit_for_composition_id;

            $raw->portion_status = $request->portion_status ?? 0;
            $raw->portion_abbreviation = $request->portion_abbreviation;
            $raw->unit_portion_id = $request->unit_portion_id;
            $raw->portion_count = $request->portion_count;

            $raw->price_unit_id = $request->price_unit_id;
            $raw->price_unit_category_id = $request->price_unit_category_id;

            $raw->serial = $request->serial;
        }

        $result = $this->updateArticle($request, $raw);
        // Если результат не массив с ошибками, значит все прошло удачно

        if (!is_array($result)) {

            $raw->display = $request->display;
            $raw->system = $request->system;

            $raw->save();

            // ПЕРЕНОС ГРУППЫ ТОВАРА В ДРУГУЮ КАТЕГОРИЮ ПОЛЬЗОВАТЕЛЕМ
            $this->changeCategory($request, $raw);

            // Метрики
            if ($request->has('metrics')) {
                // dd($request);

                $metrics_insert = [];
                foreach ($request->metrics as $metric_id => $value) {
                    if (is_array($value)) {
                        $metrics_insert[$metric_id]['value'] = implode(',', $value);
                    } else {
//                        if (!is_null($value)) {
                            $metrics_insert[$metric_id]['value'] = $value;
//                        }
                    }
                }
                $raw->metrics()->syncWithoutDetaching($metrics_insert);
            }


            // Если ли есть
            if ($request->cookie('backlink') != null) {
                $backlink = Cookie::get('backlink');
                return Redirect($backlink);
            }

            return redirect()->route('raws.index');
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
        $raw = Raw::with([
            'compositions.goods',
        ])
        ->moderatorLimit($answer)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod('destroy'), $raw);

        if ($raw) {

            $raw->archive = true;

            // Скрываем бога
            $raw->editor_id = hideGod($request->user());
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

    public function replicate(Request $request, $id)
    {
        $raw = Raw::findOrFail($id);

        $raw->load('article');
        $article = $raw->article;
        $new_article = $this->replicateArticle($request, $raw);

        $new_raw = $raw->replicate();
        $new_raw->article_id = $new_article->id;
        $new_raw->save();

        $raw->load('metrics');
        if ($raw->metrics->isNotEmpty()) {
            $metrics_insert = [];
            foreach ($raw->metrics as $metric) {
                $metrics_insert[$metric->id]['value'] = $metric->pivot->value;
            }
            $res = $new_raw->metrics()->attach($metrics_insert);
        }

        if($article->kit) {
            $article->load('raws');
            if ($article->raws->isNotEmpty()) {
                $raws_insert = [];
                foreach ($article->raws as $raw) {
                    $raws_insert[$raw->id]['value'] = $raw->pivot->value;
                }
                $res = $new_article->raws()->attach($raws_insert);
            }
        }

        return redirect()->route('raws.index');
    }

    // --------------------------------------------- Ajax -------------------------------------------------

    public function ajax_get_raw(Request $request)
    {
        $raw = Raw::with([
            'unit_portion',
            'article.group.unit',
            'article.unit_weight',
            'category'
        ])
        ->find($request->id);

        return view('products.articles.goods.raws.raw_input', compact('raw'));
    }

    // Добавляем состав
    public function ajax_get_category_raw(Request $request)
    {

        $raw = Raw::with([
            'unit_portion',
            'article.group.unit',
            'article.unit_weight',
            'category'
        ])
        ->findOrFail($request->id);

        return view('products.articles_categories.goods_categories.raws.raw_tr', compact('raw'));
    }
}
