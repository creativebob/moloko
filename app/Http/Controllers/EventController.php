<?php

namespace App\Http\Controllers;

use App\Event;
use App\EventsCategory;
use App\Http\Controllers\Traits\Processable;
use App\Http\Requests\System\EventStoreRequest;
use App\Http\Requests\System\EventUpdateRequest;
use App\Manufacturer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class EventController extends Controller
{
    protected $entityAlias;
    protected $entityDependence;

    /**
     * EventController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->entityAlias = 'events';
        $this->entityDependence = false;
        $this->class = Event::class;
    }

    use Processable;

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
        $this->authorize(getmethod(__FUNCTION__), Event::class);

        // Включение контроля активного фильтра
        $filter_url = autoFilter($request, $this->entityAlias);
        if (($filter_url != null) && ($request->filter != 'active')) {
            Cookie::queue(Cookie::forget('filter_' . $this->entityAlias));
            return Redirect($filter_url);
        }

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));
        // dd($answer);

        // -------------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -------------------------------------------------------------------------------------------------------------

        $events = Event::with([
            'author',
            'company',
            'process' => function ($q) {
                $q->with([
                    'group',
                    'photo',
                    'impacts'
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
//        ->filter($request, 'author_id')
            // ->filter($request, 'events_category_id', 'process.product')
            // ->filter($request, 'events_product_id', 'process')
            ->where('archive', false)
//        ->select($columns)
            ->orderBy('moderation', 'desc')
            ->oldest('sort')
            ->paginate(30);
        // dd($events);

        // -----------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ------------------------------------------------------------------------------
        // -----------------------------------------------------------------------------------------------------------

        $filter = setFilter($this->entityAlias, $request, [
            'author',               // Автор записи
            // 'events_category',    // Категория услуги
            // 'events_product',     // Группа услуги
            // 'date_interval',     // Дата обращения
            'booklist'              // Списки пользователя
        ]);

        // Инфо о странице
        $pageInfo = pageInfo($this->entityAlias);

        return view('products.processes.common.index.index', [
            'items' => $events,
            'pageInfo' => $pageInfo,
            'class' => Event::class,
            'entity' => $this->entityAlias,
            'category_entity' => 'events_categories',
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
        $this->authorize('index', Event::class);

        // Включение контроля активного фильтра
        $filter_url = autoFilter($request, $this->entityAlias);

        if (($filter_url != null) && ($request->filter != 'active')) {
            Cookie::queue(Cookie::forget('filter_' . $this->entityAlias));
            return Redirect($filter_url);
        }

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod('index'));

        // -----------------------------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -----------------------------------------------------------------------------------------------------------------------------

        $events = Event::with([
            'author',
            'company',
            'process' => function ($q) {
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
//         dd($events);

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

        return view('products.processes.common.index.index', [
            'items' => $events,
            'pageInfo' => $pageInfo,
            'class' => Event::class,
            'entity' => $this->entityAlias,
            'category_entity' => 'events_categories',
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
        $this->authorize(getmethod(__FUNCTION__), Event::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, 'index');

        // Главный запрос
        $eventsCategories = EventsCategory::withCount('manufacturers')
            ->with('manufacturers')
            ->moderatorLimit($answer)
            ->companiesLimit($answer)
            ->authors($answer)
            ->systemItem($answer)
            ->orderBy('sort', 'asc')
            ->get();

        if ($eventsCategories->count() == 0) {

            // Описание ошибки
            $ajax_error = [];
            $ajax_error['title'] = "Обратите внимание!";
            $ajax_error['text'] = "Для начала необходимо создать категории событий. А уже потом будем добавлять события. Ок?";
            $ajax_error['link'] = "/admin/events_categories";
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

        return view('products.processes.common.create.create', [
            'item' => Event::make(),
            'title' => 'Добавление события',
            'entity' => $this->entityAlias,
            'category_entity' => 'events_categories',
            'units_category_default' => 6,
            'unit_default' => 32,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param EventStoreRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(EventStoreRequest $request)
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Event::class);

        logs('operations')
            ->info('========================================== НАЧИНАЕМ ЗАПИСЬ СОБЫТИЯ ==============================================');

        $eventsCategory = EventsCategory::find($request->category_id);
        // dd($eventsCategory->load('groups'));
        $process = $this->storeProcess($request, $eventsCategory);

        if ($process) {

            $data = $request->input();
            $data['process_id'] = $process->id;
            $event = Event::create($data);

            if ($event) {

                // Пишем куки состояния
                // $mass = [
                //     'goods_category' => $goods_category_id,
                // ];
                // Cookie::queue('conditions_goods_category', $goods_category_id, 1440);

                logs('operations')
                    ->info('Записали событие с id: ' . $event->id);
                logs('operations')
                    ->info('Автор: ' . $event->author->name . ' id: ' . $event->author_id . ', компания: ' . $event->company->name . ', id: ' . $event->company_id);
                logs('operations')
                    ->info('========================================== КОНЕЦ ЗАПИСИ СОБЫТИЯ ==============================================

                    ');

                // dd($request->quickly);
                if ($request->quickly == 1) {
                    return redirect()->route('events.index');
                } else {
                    return redirect()->route('events.edit', $event->id);
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
        $event = Event::moderatorLimit($answer)
            ->find($id);
        // dd($event);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $event);

        $event->load([
            'process' => function ($q) {
                $q->with([
                    'unit',
                    'seo.childs.params',
                ]);
            },
            'category' => function ($q) {
                $q->with([
                    'metrics'
                ]);
            },
        ]);
//        dd($event);
        if (empty($event)) {
            abort(403, __('errors.not_found'));
        }

        $process = $event->process;
        // dd($process);

        // Получаем настройки по умолчанию
        $settings = $this->getPhotoSettings($this->entityAlias);
//        dd($settings);

        // Инфо о странице
        $pageInfo = pageInfo($this->entityAlias);
        // dd($pageInfo);

        return view('products.processes.common.edit.edit', [
            'title' => 'Редактировать событие',
            'item' => $event,
            'process' => $process,
            'pageInfo' => $pageInfo,
            'settings' => $settings,
            'entity' => $this->entityAlias,
            'category_entity' => 'events_categories',
            'categories_select_name' => 'events_category_id',
            'previous_url' => url()->previous()
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param EventUpdateRequest $request
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(EventUpdateRequest $request, $id)
    {
        // Получаем из сессии необходимые данные (Функция находится в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $event = Event::moderatorLimit($answer)
            ->find($id);
//        dd($event);
        if (empty($event)) {
            abort(403, __('errors.not_found'));
        }

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $event);

        $process = $event->process;
        // dd($process);

        $result = $this->updateProcess($request, $event);
        // Если результат не массив с ошибками, значит все прошло удачно
        if (!is_array($result)) {

            // ПЕРЕНОС ГРУППЫ В ДРУГУЮ КАТЕГОРИЮ ПОЛЬЗОВАТЕЛЕМ
            $this->changeCategory($request, $event);

            $data = $request->input();
            $event->update($data);

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
                $event->metrics()->syncWithoutDetaching($metrics_insert);
            }

            // Если ли есть
            if ($request->cookie('backlink') != null) {
                $backlink = Cookie::get('backlink');
                return Redirect($backlink);
            }

            if ($request->has('previous_url')) {
                return redirect($request->previous_url);
            }

            return redirect()->route('events.index');
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
        $event = Event::moderatorLimit($answer)
            ->find($id);
//        dd($event);
        if (empty($event)) {
            abort(403, __('errors.not_found'));
        }

        // Подключение политики
        $this->authorize(getmethod('destroy'), $event);

        $event->archive = true;

        // Скрываем бога
        $event->editor_id = hideGod($request->user());
        $event->save();

        if ($event) {
            return redirect()->route('events.index');
        } else {
            abort(403, __('errors.archive'));
        }
    }

    public function replicate(Request $request, $id)
    {
        $event = Event::find($id);

        $event->load('process');
        $process = $event->process;
        $new_process = $this->replicateProcess($request, $event);

        $new_event = $event->replicate();
        $new_event->process_id = $new_process->id;
        $new_event->save();

        $event->load('metrics');
        if ($event->metrics->isNotEmpty()) {
            $metrics_insert = [];
            foreach ($event->metrics as $metric) {
                $metrics_insert[$metric->id]['value'] = $metric->pivot->value;
            }
            $res = $new_event->metrics()->attach($metrics_insert);
        }

        if ($process->kit) {
            $process->load('events');
            if ($process->events->isNotEmpty()) {
                $events_insert = [];
                foreach ($process->events as $event) {
                    $events_insert[$event->id]['value'] = $event->pivot->value;
                }
                $res = $new_process->events()->attach($events_insert);
            }
        }

        return redirect()->route('events.index');
    }

    // --------------------------------------------- Ajax -------------------------------------------------

    public function ajax_get_event(Request $request)
    {
        $event = Event::with([
            'process.group.unit',
            'category'
        ])
            ->find($request->id);

        return view('products.processes.services.events.event_input', compact('event'));
    }

    // Добавляем состав
    public function ajax_get_category_event(Request $request)
    {

        $event = Event::with([
            'process.group.unit',
            'category'
        ])
            ->find($request->id);

        return view('products.processes_categories.services_categories.events.event_tr', compact('event'));
    }
}
