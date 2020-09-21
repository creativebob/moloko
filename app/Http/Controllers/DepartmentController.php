<?php

namespace App\Http\Controllers;

// Модели
use App\Department;
use App\City;
use App\Http\Controllers\System\Traits\Phonable;
use App\Position;
use App\Staffer;
use App\Page;
use App\Right;
use App\Schedule;
use App\Worktime;
use App\Location;
use App\ScheduleEntity;

// Подрубаем трейт перезаписи сессии
use App\Http\Controllers\Traits\RewriteSessionDepartments;

// Валидация
use Illuminate\Http\Request;
use App\Http\Requests\System\DepartmentRequest;

// Специфические классы
use Illuminate\Support\Facades\Storage;

class DepartmentController extends Controller
{

    /**
     * DepartmentController constructor.
     * @param Department $department
     */
    public function __construct(Department $department)
    {
        $this->middleware('auth');
        $this->department = $department;
        $this->entity_alias = with(new Department)->getTable();;
        $this->entity_dependence = true;
        $this->class = Department::class;
        $this->model = 'App\Department';
        $this->type = 'table';
    }

    // Подключаем трейт перезаписи списк отделов (филиалов) в сессии пользователя
    use RewriteSessionDepartments;
    use Phonable;

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_departments = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));
        $answer_staff = operator_right('staff', true, getmethod(__FUNCTION__));
        $answer_positions = operator_right('positions', false, getmethod(__FUNCTION__));

        // -----------------------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -----------------------------------------------------------------------------------------------------------------------

        $departments = Department::with([
            'staff' => function ($query) use ($answer_staff, $answer_positions) {
                $query->with([
                    'user',
                    'position' => function ($q) use ($answer_positions) {
                        $q->moderatorLimit($answer_positions)
                            ->companiesLimit($answer_positions)
                            ->authors($answer_positions)
                            ->systemItem($answer_positions) // Фильтр по системным записям
                            ->template($answer_positions) // Выводим шаблоны альбомов
                            ->orderBy('moderation', 'desc')
                            ->orderBy('sort', 'asc');
                    }
                ])
                ->moderatorLimit($answer_staff)
                ->companiesLimit($answer_staff)
                ->filials($answer_staff) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
                ->authors($answer_staff)
                ->systemItem($answer_staff) // Фильтр по системным записям
                ->where('archive', false)
                ->orderBy('moderation', 'desc')
                ->orderBy('sort', 'asc');
            }
        ])
        ->withCount('staff')
        ->moderatorLimit($answer_departments)
        ->companiesLimit($answer_departments)
        ->filials($answer_departments) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
        ->authors($answer_departments)
        ->systemItem($answer_departments) // Фильтр по системным записям
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->get();
//         dd($departments);
        // ->groupBy('parent_id');

        // Создаем масив где ключ массива является ID меню
        // $departments_rights = [];
        // $departments_rights = $departments->keyBy('id');

        // Отдаем Ajax
        if ($request->ajax()) {

            return view('system.pages.hr.departments.filials_list',
                [
                    'departments' => $departments,
                    'entity' => $this->entity_alias,
                    'class' => $this->model,
                    'type' => $this->type,
                    'count' => $departments->count(),
                    'id' => $request->id,
                    'item' => $request->item
                ]
            );
        }

        // Отдаем на шаблон
        return view('system.pages.hr.departments.index',
            [
                'departments' => $departments,
                'pageInfo' => pageInfo($this->entity_alias),
                'class' => $this->class,
                'type' => $this->type,
                'filter' => setFilter($this->entity_alias, $request, [
                    'booklist'
                ]),
                'id' => $request->id,
                'item' => $request->item
            ]
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        if (isset($request->parent_id)) {
            return view('system.pages.hr.departments.department.create', [
                'department' => Department::make(),
                'entity' => $this->entity_alias,
                'parent_id' => $request->parent_id,
                'filial_id' => $request->filial_id,
            ]);
        } else {
            return view('system.pages.hr.departments.filial.create', [
                'department' => Department::make(),
                'pageInfo' => pageInfo($this->entity_alias),
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param DepartmentRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(DepartmentRequest $request)
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        $data = $request->validated();
        $data['location_id'] = create_location($request);
        $department = Department::create($data);

        if ($department) {

            if (is_null($department->filial_id)) {
                $this->setCities($department);
            }

            // Расписание
            setSchedule($request, $department);

            // Телефоны
            $this->savePhones($department);
//            add_phones($request, $department);

            // Перезаписываем сессию: меняем список филиалов и отделов на новый
            $this->RSDepartments($user);

            // Переадресовываем на index
            return redirect()->route('departments.index', ['id' => $department->id, 'item' => $this->entity_alias]);

        } else {
            abort(403, 'Ошибка при записи!');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param $id
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $department = Department::with([
            'cities' => function ($q) {
                $q->with([
                   'area',
                   'region',
                   'country'
                ]);
            }
        ])
        ->moderatorLimit($answer)
            ->find($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $department);

        if (isset($department->parent_id)) {
            return view('system.pages.hr.departments.department.edit', [
                'department' => $department,
                'title' => isset($department->parent_id) ? 'Редактирование отдела' : 'Редактирование филиала',
                'entity' => $this->entity_alias,
                'parent_id' => $department->parent_id,
                'filial_id' => $department->filial_id,
            ]);
        } else {
            return view('system.pages.hr.departments.filial.edit', [
                'department' => $department,
                'pageInfo' => pageInfo($this->entity_alias),
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param DepartmentRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(DepartmentRequest $request, $id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $department = Department::moderatorLimit($answer)
        ->find($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $department);

        $data = $request->validated();
        $data['location_id'] = create_location($request);
        $result = $department->update($data);
//        dd($department);

        if ($result) {
            // Расписание
            setSchedule($request, $department);

            // Телефон
            $this->savePhones($department);
//            add_phones($request, $department);

            if (is_null($department->filial_id)) {
                $this->setCities($department);
            }

            // Переадресовываем на index
            return redirect()->route('departments.index', ['id' => $department->id, 'item' => $this->entity_alias]);
        } else {
            abort(403, 'Ошибка при обновлении отдела!');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $department = Department::with([
            'staff',
            'users',
            'childs'
        ])
        ->moderatorLimit($answer)
        ->find($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $department);

        // Получаем пользователя
        $user = $request->user();

        $parent = $department->parent_id;

        $department->delete();

        if ($department) {

            // Перезаписываем сессию: меняем список филиалов и отделов на новый
            $this->RSDepartments($user);

            // Переадресовываем на index
            return redirect()->route('departments.index', ['id' => $parent, 'item' => $this->entity_alias]);
        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при удалении!'
            ];
        }

    }

    /**
     * Запись городов зоны ответственности с проверкой на существование в них города филиала
     *
     * @param $department
     */
    public function setCities($department)
    {
        $department->load('location');
        $cities = request()->cities;
        if (! in_array($department->location->city_id, $cities)) {
            $cities[] = $department->location->city_id;
        }
        $department->cities()->sync($cities);
    }

    public function ajax_check(Request $request)
    {

        // Проверка отдела в нашей базе данных
        $result_count = Department::where([
            'company_id' => $request->user()->company_id,
            'name' => $request->name,
            'filial_id' => $request->filial_id
        ])
        ->where('id', '!=', $request->id)
        ->count();

        return response()->json($result_count);
    }

    public function departments_list(Request $request)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, true, 'index');

        $departments_list = Department::moderatorLimit($answer)->whereId($request->filial_id)
        ->orWhere('filial_id', $request->filial_id)
        ->pluck('name', 'id');

        echo json_encode($departments_list, JSON_UNESCAPED_UNICODE);
    }

    public function ajax_get_filials_for_catalogs_service(Request $request)
    {
        $catalog_id = $request->catalog_id;
        return view('products.processes.services.prices.filials', compact('catalog_id'));
    }

    public function ajax_get_filials_for_catalogs_goods(Request $request)
    {
        $catalog_id = $request->catalog_id;
        return view('products.articles.goods.prices.filials', compact('catalog_id'));
    }
}
