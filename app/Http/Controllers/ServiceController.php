<?php

namespace App\Http\Controllers;

// Модели
use App\Service;
use App\ServicesCategory;
use App\Process;
use App\Manufacturer;

// Валидация
use Illuminate\Http\Request;
use App\Http\Requests\ServiceRequest;
use App\Http\Requests\ProcessRequest;

// Куки
use Illuminate\Support\Facades\Cookie;

// Трейты
use App\Http\Controllers\Traits\Processable;

use Illuminate\Support\Facades\Log;

class ServiceController extends Controller
{

    // Настройки сконтроллера
    public function __construct(Service $service)
    {
        $this->middleware('auth');
        $this->service = $service;
        $this->class = Service::class;
        $this->model = 'App\Service';
        $this->entity_alias = with(new $this->class)->getTable();
        $this->entity_dependence = false;
    }

    use Processable;

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
            'process_id',
            'category_id',
            'author_id',
            'company_id',
            'display',
            'system'
        ];

        $services = Service::with([
            'author',
            'company',
            'process' => function ($q) {
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
        ->template($answer)
        ->booklistFilter($request)
        ->filter($request, 'author_id')
        // ->filter($request, 'services_category_id', 'process.product')
        // ->filter($request, 'services_product_id', 'process')
        ->where('archive', false)
        ->select($columns)
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->paginate(30);
        // dd($services);


        // -----------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ------------------------------------------------------------------------------
        // -----------------------------------------------------------------------------------------------------------

        $filter = setFilter($this->entity_alias, $request, [
            'author',               // Автор записи
            // 'services_category',    // Категория услуги
            // 'services_product',     // Группа услуги
            // 'date_interval',     // Дата обращения
            'booklist'              // Списки пользователя
        ]);


        // Инфо о странице
        $page_info = pageInfo($this->entity_alias);

        return view('products.processes.common.index.index', [
            'items' => $services,
            'page_info' => $page_info,
            'class' => $this->class,
            'entity' => $this->entity_alias,
            'category_entity' => 'services_categories',
            'filter' => $filter,
        ]);
    }

    public function create(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('services_categories', false, 'index');

        // Главный запрос
        $services_categories = ServicesCategory::withCount('manufacturers')
        ->with('manufacturers')
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer)
        ->template($answer)
        ->orderBy('sort', 'asc')
        ->get();

        if ($services_categories->count() == 0){

            // Описание ошибки
            $ajax_error = [];
            $ajax_error['title'] = "Обратите внимание!";
            $ajax_error['text'] = "Для начала необходимо создать категории услуг. А уже потом будем добавлять рабочие услги. Ок?";
            $ajax_error['link'] = "/admin/services_categories";
            $ajax_error['title_link'] = "Идем в раздел категорий";

            return view('ajax_error', compact('ajax_error'));
        }

        if ($request->user()->company_id != null) {

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
        }

        return view('products.processes.common.create.create', [
            'item' => new $this->class,
            'title' => 'Добавление услуги',
            'entity' => $this->entity_alias,
            'category_entity' => 'services_categories',
        ]);
    }

    public function store(ProcessRequest $request)
    {

//         dd($request);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        Log::channel('operations')
        ->info('========================================== НАЧИНАЕМ ЗАПИСЬ УСЛУГИ ==============================================');

        $services_category = ServicesCategory::findOrFail($request->category_id);
        // dd($services_category->load('groups'));
        $process = $this->storeProcess($request, $services_category);

        if ($process) {

            $data = $request->input();
            $data['process_id'] = $process->id;
            $service = (new Service())->create($data);

            if ($service) {

                $workflows = $services_category->workflows->pluck('id')->toArray();
                $process->workflows()->sync($workflows);

                // Пишем куки состояния
                // $mass = [
                //     'goods_category' => $goods_category_id,
                // ];
                // Cookie::queue('conditions_goods_category', $goods_category_id, 1440);
                Log::channel('operations')
                ->info('Записали услугу с id: ' . $service->id);
                Log::channel('operations')
                ->info('Автор: ' . $service->author->name . ' id: ' . $service->author_id .  ', компания: ' . is_null($service->company) ? 'шаблон' : $service->company->name . ', id: ' . $service->company_id);
                Log::channel('operations')
                ->info('========================================== КОНЕЦ ЗАПИСИ УСЛУГИ ==============================================

                    ');
                // dd($request->quickly);
                if ($request->quickly == 1) {
                    return redirect()->route('services.index');
                } else {
                    return redirect()->route('services.edit', ['id' => $service->id]);
                }
            } else {
                abort(403, 'Ошибка записи услуги');
            }
        } else {
            abort(403, 'Ошибка записи информации услуги');
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
        $service = Service::moderatorLimit($answer)
        ->findOrFail($id);
        // dd($service);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $service);

        $process = $service->process;
        // dd($process);

        $service->load('prices');

        // Получаем настройки по умолчанию
        $settings = getSettings($this->entity_alias);

        // Инфо о странице
        $page_info = pageInfo($this->entity_alias);
        // dd($page_info);

        return view('products.processes.common.edit.edit', [
            'title' => 'Редактировать услугу',
            'item' => $service,
            'process' => $process,
            'page_info' => $page_info,
            'settings' => $settings,
            'entity' => $this->entity_alias,
            'category_entity' => 'services_categories',
            'categories_select_name' => 'services_category_id',
        ]);
    }

    public function update(ProcessRequest $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находится в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $service = Service::moderatorLimit($answer)
        ->findOrFail($id);
        // dd($service);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $service);

        $process = $service->process;
        // dd($process);


        $result = $this->updateProcess($request, $service);
        // Если результат не массив с ошибками, значит все прошло удачно
        if (!is_array($result)) {

            $service->serial = $request->serial;
            $service->display = $request->display;
            $service->system = $request->system;
            $service->save();

            // ПЕРЕНОС ГРУППЫ ТОВАРА В ДРУГУЮ КАТЕГОРИЮ ПОЛЬЗОВАТЕЛЕМ
            $this->changeCategory($request, $service);

            // // Каталоги
            // $data = [];
            // if (isset($request->catalogs_items)) {

            //     $user = $request->user();

            //     foreach ($request->catalogs_items as $catalog_id => $items) {
            //         foreach ($items as $item_id) {
            //             $data[(int) $item_id] = [
            //                 'catalogs_service_id' => $catalog_id,
            //                 'price' => $process->price_default,
            //                 'company_id' => $user->company_id,
            //                 'filial_id' => $user->filial_id,
            //                 'author_id' => hideGod($user),
            //             ];
            //         }
            //     }
            // }
            // // dd($data);
            // $service->prices()->sync($data);

            // Если ли есть
            if ($request->cookie('backlink') != null) {
                $backlink = Cookie::get('backlink');
                return Redirect($backlink);
            }

            return redirect()->route('services.index');
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
        $service = Service::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod('destroy'), $service);

        if ($service) {

            $service->archive = true;

            // Скрываем бога
            $service->editor_id = hideGod($request->user());
            $service->save();

            if ($service) {
                return redirect()->route('services.index');
            } else {
                abort(403, 'Ошибка при архивации сырья');
            }
        } else {
            abort(403, 'Сырьё не найдено');
        }
    }

    // --------------------------------------------- Ajax -------------------------------------------------

    public function ajax_get_service(Request $request)
    {
        $service = Service::with([
            'process.group.unit',
            'category'
        ])
            ->find($request->id);

        return view('products.processes.services.services.service_input', compact('service'));
    }
}
