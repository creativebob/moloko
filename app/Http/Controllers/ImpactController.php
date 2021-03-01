<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\Articlable;
use App\Http\Requests\System\ImpactStoreRequest;
use App\Http\Requests\System\ImpactUpdateRequest;
use App\Impact;
use App\ImpactsCategory;
use App\Manufacturer;
use Illuminate\Http\Request;

class ImpactController extends Controller
{
    protected $entityAlias;
    protected $entityDependence;

    /**
     * ImpactController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->entityAlias = 'impacts';
        $this->entityDependence = false;

        // TODO - 19.01.21 - СТарый код для поиска
        $this->class = Impact::class;
        $this->entity_alias = 'impacts';
        $this->entity_dependence = false;
    }

    use Articlable;

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Impact::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));
        // dd($answer);

        // -------------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -------------------------------------------------------------------------------------------------------------

        $impacts = Impact::with([
            'author',
            'company',
            'article' => function ($q) {
                $q->with([
                    'group',
                    'photo'
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

                ->filter()
//            ->booklistFilter($request)
//        ->filter($request, 'author_id')
            // ->filter($request, 'impacts_category_id', 'article.product')
            // ->filter($request, 'impacts_product_id', 'article')
            ->where('archive', false)
//        ->select($columns)
            ->orderBy('moderation', 'desc')
            ->oldest('sort')
            ->paginate(30);
        // dd($impacts);

        // -----------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ------------------------------------------------------------------------------
        // -----------------------------------------------------------------------------------------------------------

        $filter = setFilter($this->entityAlias, $request, [
            'author',               // Автор записи
            // 'impacts_category',    // Категория услуги
            // 'impacts_product',     // Группа услуги
            // 'date_interval',     // Дата обращения
            'booklist'              // Списки пользователя
        ]);

        // Инфо о странице
        $pageInfo = pageInfo($this->entityAlias);

        return view('products.articles.common.index.index', [
            'items' => $impacts,
            'pageInfo' => $pageInfo,
            'class' => Impact::class,
            'entity' => $this->entityAlias,
            'category_entity' => 'impacts_categories',
            'filter' => $filter,
        ]);
    }

    /**
     * Отображение архивных записей
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function archives(Request $request)
    {

        // Подключение политики
        $this->authorize('index', Impact::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod('index'));

        // -----------------------------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -----------------------------------------------------------------------------------------------------------------------------

        $impacts = Impact::with([
            'author',
            'company',
            'article' => function ($q) {
                $q->with([
                    'group',
                    'photo'
                ]);
            },
            'category'
        ])
            ->moderatorLimit($answer)
            ->companiesLimit($answer)
            ->authors($answer)
            ->systemItem($answer)
            ->booklistFilter($request)
//            ->filter($request, 'author_id')
//
//            ->whereHas('article', function($q) use ($request){
//                $q->filter($request, 'articles_group_id');
//            })
//
//            ->filter($request, 'category_id')
            // ->filter($request, 'goods_product_id', 'article')
            ->where('archive', true)
//        ->select($columns)
            ->orderBy('moderation', 'desc')
            ->oldest('id')
            ->paginate(30);
//         dd($impacts);

        // -----------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ------------------------------------------------------------------------------
        // -----------------------------------------------------------------------------------------------------------

        $filter = setFilter($this->entityAlias, $request, [
            'author',               // Автор записи
//            'goods_category',       // Категория товара
//            'articles_group',    // Группа артикула
            'booklist'              // Списки пользователя
        ]);

        // Окончание фильтра -----------------------------------------------------------------------------------------

        // Инфо о странице
        $pageInfo = pageInfo($this->entityAlias);

        return view('products.articles.common.index.index', [
            'items' => $impacts,
            'pageInfo' => $pageInfo,
            'class' => Impact::class,
            'entity' => $this->entityAlias,
            'category_entity' => 'impacts_categories',
            'filter' => $filter,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create()
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Impact::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, 'index');

        // Главный запрос
        $impactsCategories = ImpactsCategory::withCount('manufacturers')
            ->with('manufacturers')
            ->moderatorLimit($answer)
            ->companiesLimit($answer)
            ->authors($answer)
            ->systemItem($answer)
            ->orderBy('sort', 'asc')
            ->get();

        if ($impactsCategories->count() == 0) {

            // Описание ошибки
            $ajax_error = [];
            $ajax_error['title'] = "Обратите внимание!";
            $ajax_error['text'] = "Для начала необходимо создать категории объектов воздействия. А уже потом будем добавлять объекты воздейтсвия. Ок?";
            $ajax_error['link'] = "/admin/impacts_categories";
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
        if ($manufacturers_count == 0) {

            // Описание ошибки
            // $ajax_error = [];
            $ajax_error['title'] = "Обратите внимание!"; // Верхняя часть модалки
            $ajax_error['text'] = "Для начала необходимо добавить производителей. А уже потом будем добавлять рабочие процессы. Ок?";
            $ajax_error['link'] = "/admin/manufacturers/create"; // Ссылка на кнопке
            $ajax_error['title_link'] = "Идем в раздел производителей"; // Текст на кнопке

            return view('ajax_error', compact('ajax_error'));
        }

        return view('products.articles.common.create.create', [
            'item' => Impact::make(),
            'title' => 'Добавление объекта воздействия',
            'entity' => $this->entityAlias,
            'category_entity' => 'impacts_categories',
            'units_category_default' => 6,
            'unit_default' => 32,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ImpactStoreRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(ImpactStoreRequest $request)
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Impact::class);

        logs('operations')
            ->info('========================================== НАЧИНАЕМ ЗАПИСЬ ОБЪЕКТА ВОЗДЕЙСТВИЯ ==============================================');

        $impactsCategory = ImpactsCategory::find($request->category_id);
        // dd($impactsCategory->load('groups'));
        $article = $this->storeArticle($request, $impactsCategory);

        if ($article) {

            $data = $request->input();
            $data['article_id'] = $article->id;
            $impact = Impact::create($data);

            if ($impact) {

                // Пишем куки состояния
                // $mass = [
                //     'goods_category' => $goods_category_id,
                // ];
                // Cookie::queue('conditions_goods_category', $goods_category_id, 1440);

                logs('operations')
                    ->info('Записали объект воздействия с id: ' . $impact->id);
                logs('operations')
                    ->info('Автор: ' . $impact->author->name . ' id: ' . $impact->author_id . ', компания: ' . $impact->company->name . ', id: ' . $impact->company_id);
                logs('operations')
                    ->info('========================================== КОНЕЦ ЗАПИСИ ОБЪЕКТА ВОЗДЕЙСТВИЯ ==============================================

                    ');

                // dd($request->quickly);
                if ($request->quickly == 1) {
                    return redirect()->route('impacts.index');
                } else {
                    return redirect()->route('impacts.edit', $impact->id);
                }
            } else {
                abort(403, __('errors.store'));
            }
        } else {
            abort(403, 'Ошибка записи информации сырья');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        // Главный запрос
        $impact = Impact::moderatorLimit($answer)
            ->find($id);
        // dd($impact);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $impact);

        $impact->load([
            'article' => function ($q) {
                $q->with([
                    'unit',
                    'parts' => function ($q) {
                        $q->with([
                            'impact' => function ($q) {
                                $q->with([
                                    'category',
                                    'unit_for_composition',
                                    'unit_portion',
                                    'costs',
                                    'article.unit',
                                ]) ;
                            },
                        ]);
                    }
                ]);
            },
            'category' => function ($q) {
                $q->with([
                    'metrics'
                ]);
            },
        ]);
//        dd($impact);
        if (empty($impact)) {
            abort(403, __('errors.not_found'));
        }

        $article = $impact->article;
        // dd($article);

        // Получаем настройки по умолчанию
        $settings = $this->getPhotoSettings($this->entityAlias);
//        dd($settings);

        // Инфо о странице
        $pageInfo = pageInfo($this->entityAlias);
        // dd($pageInfo);

        return view('products.articles.common.edit.edit', [
            'title' => 'Редактировать объект воздействия',
            'item' => $impact,
            'article' => $article,
            'pageInfo' => $pageInfo,
            'settings' => $settings,
            'entity' => $this->entityAlias,
            'category_entity' => 'impacts_categories',
            'categories_select_name' => 'impacts_category_id',
            'previous_url' => url()->previous()
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ImpactUpdateRequest $request
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(ImpactUpdateRequest $request, $id)
    {
        // Получаем из сессии необходимые данные (Функция находится в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $impact = Impact::moderatorLimit($answer)
            ->find($id);
//        dd($impact);
        if (empty($impact)) {
            abort(403, __('errors.not_found'));
        }

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $impact);

        $article = $impact->article;
        // dd($article);

        $result = $this->updateArticle($request, $impact);
        // Если результат не массив с ошибками, значит все прошло удачно
        if (!is_array($result)) {

            // ПЕРЕНОС ГРУППЫ В ДРУГУЮ КАТЕГОРИЮ ПОЛЬЗОВАТЕЛЕМ
            $this->changeCategory($request, $impact);

            $data = $request->input();
            $impact->update($data);

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
                $impact->metrics()->syncWithoutDetaching($metrics_insert);
            }

            return redirect()->route('impacts.index');
        } else {
            return back()
                ->withErrors($result)
                ->withInput();
        }
    }

    /**
     * Архивация указанного ресурса.
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function archive(Request $request, $id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, 'delete');

        // ГЛАВНЫЙ ЗАПРОС:
        $impact = Impact::moderatorLimit($answer)
            ->find($id);
//        dd($impact);
        if (empty($impact)) {
            abort(403, __('errors.not_found'));
        }

        // Подключение политики
        $this->authorize(getmethod('destroy'), $impact);

        $impact->archive = true;

        // Скрываем бога
        $impact->editor_id = hideGod($request->user());
        $impact->save();

        if ($impact) {
            return redirect()->route('impacts.index');
        } else {
            abort(403, __('errors.archive'));
        }
    }

    public function replicate(Request $request, $id)
    {
        $impact = Impact::find($id);

        $impact->load('article');
        $article = $impact->article;
        $new_article = $this->replicateArticle($request, $impact);

        $new_impact = $impact->replicate();
        $new_impact->article_id = $new_article->id;
        $new_impact->save();

//        $impact->load('metrics');
//        if ($impact->metrics->isNotEmpty()) {
//            $metrics_insert = [];
//            foreach ($impact->metrics as $metric) {
//                $metrics_insert[$metric->id]['value'] = $metric->pivot->value;
//            }
//            $res = $new_impact->metrics()->attach($metrics_insert);
//        }

        if ($article->kit) {
            $article->load('impacts');
            if ($article->impacts->isNotEmpty()) {
                $impacts_insert = [];
                foreach ($article->impacts as $impact) {
                    $impacts_insert[$impact->id]['value'] = $impact->pivot->value;
                }
                $res = $new_article->impacts()->attach($impacts_insert);
            }
        }

        return redirect()->route('impacts.index');
    }
}
