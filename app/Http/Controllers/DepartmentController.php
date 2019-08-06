<?php

namespace App\Http\Controllers;

// Модели
use App\Department;
use App\City;
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
use App\Http\Requests\DepartmentRequest;

// Специфические классы
use Illuminate\Support\Facades\Storage;

class DepartmentController extends Controller
{

    // Настройки сконтроллера
    public function __construct(Department $department)
    {
        $this->middleware('auth');
        $this->department = $department;
        $this->entity_alias = with(new Department)->getTable();;
        $this->entity_dependence = true;
        $this->class = Department::class;
        $this->model = 'App\Department';
        $this->type = 'menu';
    }

    // Подключаем трейт перезаписи списк отделов (филиалов) в сессии пользователя
    use RewriteSessionDepartments;

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

        $departments = Department::with(['staff' => function ($query) use ($answer_staff, $answer_positions) {
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
            }])
            ->moderatorLimit($answer_staff)
            ->companiesLimit($answer_staff)
            ->filials($answer_staff) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
            ->authors($answer_staff)
            ->systemItem($answer_staff) // Фильтр по системным записям
            ->orderBy('moderation', 'desc')
            ->orderBy('sort', 'asc');
        }])
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

            return view('departments.filials_list',
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
        return view('departments.index',
            [
                'departments' => $departments,
                'page_info' => pageInfo($this->entity_alias),
                'class' => $this->class,
                'type' => $this->type,
                'filter' => setFilter($this->entity_alias, $request, [
                    'booklist'
                ]),
            ]
        );
    }

    public function create(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        return view('departments.create', [
            'department' => new $this->class,
            'entity' => $this->entity_alias,
            'title' => isset($request->parent_id) ? 'Добавление отдела / должности' : 'Добавление филиала',
            'parent_id' => $request->parent_id,
            'filial_id' => $request->filial_id
        ]);
    }

    public function store(DepartmentRequest $request)
    {

        // dd($request);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        $department = new Department;

        if (isset($request->city_name)) {
            $department->location_id = create_location($request);
        }

        $department->company_id = $user->company_id;

        // Имя филиала / отдела
        $first = mb_substr($request->name,0,1, 'UTF-8');
        $last = mb_substr($request->name,1);
        $first = mb_strtoupper($first, 'UTF-8');
        $department->name = $first.$last;

        if (isset($request->parent_id)) {
            $department->filial_id = $request->filial_id;
            $department->parent_id = $request->parent_id;
            $status = 'отдела';
        } else {
            $status = 'филиала';
        }

        $department->code_map = $request->code_map;
        $department->email = $request->email;

        // Отображение на сайте
        $department->display = $request->has('display');
        $department->system = $request->has('system');

        $department->author_id = hideGod($user);

        $department->save();

        if ($department) {

            // Расписание
            setSchedule($request, $department);

            // Телефон
            add_phones($request, $department);

            // Перезаписываем сессию: меняем список филиалов и отделов на новый
            $this->RSDepartments($user);

            // Переадресовываем на index
            return redirect()->route('departments.index', ['id' => $department->id, 'item' => $this->entity_alias]);

        } else {
            abort(403, 'Ошибка при записи!');
        }
    }

    public function show($id)
    {

    }

    public function edit($id)
    {

        $department = Department::moderatorLimit(operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__)))->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $department);

        \JavaScript::put([
            'city' => $department->location->city,
        ]);

        return view('departments.edit', [
            'department' => $department,
            'entity' => $this->entity_alias,
            'title' => isset($department->parent_id) ? 'Редактирование отдела' : 'Редактирование филиала',
            'parent_id' => $department->parent_id,
            'category_id' => $department->category_id
        ]);
    }

    public function update(DepartmentRequest $request, $id)
    {

        $department = Department::moderatorLimit(operator_right($this->entity_alias, $this->entity_alias, getmethod(__FUNCTION__)))
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $department);

        if (isset($department->location_id)) {

            // Обновляем локацию
            $department = update_location($request, $department);
        }

        // Имя филиала / отдела
        $first = mb_substr($request->name,0,1, 'UTF-8'); //первая буква
        $last = mb_substr($request->name,1); //все кроме первой буквы
        $first = mb_strtoupper($first, 'UTF-8');
        $department->name = $first.$last;

        $department->editor_id = hideGod($request->user());

        $status = isset($request->parent_id) ? 'отдела' : 'филиала';
        $department->parent_id = $request->parent_id;

        $department->code_map = $request->code_map;
        $department->email = $request->email;

        // Отображение на сайте
        $department->display = $request->has('display');
        $department->system = $request->has('system');

        $department->save();

        if ($department) {
            // Расписание
            setSchedule($request, $department);

            // Телефон
            add_phones($request, $department);
            // Переадресовываем на index
            return redirect()->route('departments.index', ['id' => $department->id, 'item' => $this->entity_alias]);
        } else {
            abort(403, 'Ошибка при обновлении отдела!');
        }
    }

    public function destroy(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $department = Department::with('staff', 'users', 'childs')
        ->moderatorLimit($answer)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $department);

        // Получаем пользователя
        $user = $request->user();

        // Скрываем бога
        $department->editor_id = hideGod($user);
        $department->save();

        $parent = $department->parent_id;

        $department = Department::destroy($id);

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
