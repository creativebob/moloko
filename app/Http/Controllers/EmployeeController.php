<?php

namespace App\Http\Controllers;

use App\Employee;
use App\Position;
use App\Staffer;
use App\Department;
use App\User;
use App\Page;
use App\Company;

// Валидация
use Illuminate\Http\Request;
use App\Http\Requests\EmployeeRequest;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;

// Общие классы
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;

// Подрубаем трейт записи и обновления пользоватля
use App\Http\Controllers\Traits\UserControllerTrait;

class EmployeeController extends Controller
{

    // Подключаем трейт записи и обновления компании
    use UserControllerTrait;

    // Настройки сконтроллера
    public function __construct(Employee $employee)
    {
        $this->middleware('auth');
        $this->employee = $employee;
        $this->class = Employee::class;
        $this->model = 'App\Employee';
        $this->entity_alias = with(new $this->class)->getTable();
        $this->entity_dependence = true;
        $this->type = 'modal';
    }

    public function index(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Включение контроля активного фильтра
        $filter_url = autoFilter($request, $this->entity_alias);
        if(($filter_url != null)&&($request->filter != 'active')){return Redirect($filter_url);};

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // Смотрим сколько филиалов в компании
        $user = $request->user();
        // -------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -------------------------------------------------------------------------------------------
        $employees = Employee::with(['company.filials', 'staffer' => function($q) {
            $q->with('position', 'filial', 'department');
        }, 'user'])
        ->moderatorLimit($answer)
        ->companiesLimit($answer)

        // Так как сущность не филиала зависимая, но по факту
        // все таки зависимая через staff, то делаем нестандартную фильтрацию (прямо в запросе)
        ->when($answer['dependence'] == true, function ($query) use ($user) {
            return $query->whereHas('staffer', function($q) use ($user){
                $q->where('filial_id', $user->filial_id);
            });
        })

        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->booklistFilter($request)
        ->filter($request, 'position_id', 'staffer')
        ->filter($request, 'department_id', 'staffer')
        ->dateIntervalFilter($request, 'date_employment')

        ->whereHas('user', function($q) use ($request){
            $q->booleanArrayFilter($request, 'access_block');
        })

        ->orderBy('moderation', 'desc')
        ->orderBy('dismissal_date', 'asc')
        ->orderBy('sort', 'asc')
        ->paginate(30);

        // -----------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ------------------------------------------------------------------------------
        // -----------------------------------------------------------------------------------------------------------

        $filter = setFilter($this->entity_alias, $request, [
            'position',             // Должность
            'department',           // Отдел
            'date_interval',        // Дата
            'access_block',         // Доступ
            'booklist'              // Списки пользователя
        ]);

        // Окончание фильтра -----------------------------------------------------------------------------------------

        // Инфо о странице
        $page_info = pageInfo($this->entity_alias);

        return view('employees.index', compact('employees', 'page_info', 'filter'));
    }

    public function create()
    {
        //Подключение политики
        $this->authorize(getmethod('create'), Employee::class);
        $this->authorize(getmethod('create'), User::class);

        // Создаем новый экземляр дилера
        $employee = new Employee;

        // Создаем новый экземляр пользователя
        $user = new User;

        // Инфо о странице
        $page_info = pageInfo($this->entity_alias);

        return view('employees.create', compact('user', 'employee', 'page_info'));
    }

    public function store(UserStoreRequest $request)
    {
        // Подключение политики
        $this->authorize(getmethod('create'), Employee::class);
        $this->authorize(getmethod('create'), User::class);
        $this->authorize(getmethod('create'), Staffer::class);

        Log::info('Будем создавать сотрудника: все необходимые права есть');

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // Получаем данные для авторизованного пользователя
        $user_auth = $request->user();

        // Скрываем бога
        $user_id = hideGod($user_auth);


        // Проверяем: свободна ли ставка  =====================================================
        Log::info('Проверяем: свободна ли ставка?');

        $staff = Staffer::with('position.roles')->findOrFail($request->staff_id);
        // dd($staff);

        if($staff->user_id != null){
            abort(403, "Ставка не свободна!");
        } else {
            Log::info('Ставка свободна!');
        };

        // Создание нового сотрудника =========================================================

        $employee = new Employee;
        $new_user = new User;

        Log::info('Подготовили новых user и employee');

        // Отдаем работу по созданию нового юзера трейту
        $user = $this->createUser($request, $new_user);

        // Запись информации по сотруднику:
        $employee->company_id = $request->user()->company->id;
        $employee->staffer_id = $request->staff_id;
        $employee->user_id = $user->id;
        $employee->author_id = $user_auth->id;

        $employee->employment_date = outPickMeUp($request->employment_date);
        $employee->dismissal_date = $request->dismissal_date == null ? null : outPickMeUp($request->dismissal_date);
        $employee->dismissal_description = $request->dismissal_description;

        $employee->display = $request->display;
        $employee->system_item = $request->system_item;
        $employee->save();

        // Если сотрудник удачно создан - занимем ставку
        if($employee){

            Log::info('Сотрудник создан - будем занимать ставку!');
            $staff->user_id = $user->id;
            $staff->save();


            // Прописываем права
            $position = $staff->position;
            $position->load('roles');

            $insert_array = [];
            foreach ($position->roles as $role) {
                $insert_array[$role->id] = [
                    'department_id' => $staff->department_id,
                    'position_id' => $staff->position_id
                ];
            }

            $user->roles()->attach($insert_array);
            Log::info('Записали роли для юзера (сотрудника)');


        } else {

            Log::info('Сотрудник не был создан!');
        };

        return redirect('/admin/employees');
    }

    public function show($id)
    {
        //
    }

    public function edit(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias,  $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $employee = Employee::with('user')->moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $employee);

        // Инфо о странице
        $page_info = pageInfo($this->entity_alias);

        return view('employees.edit', compact('employee', 'page_info'));
    }


    public function update(UserUpdateRequest $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias,  $this->entity_dependence, getmethod(__FUNCTION__));

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        // ГЛАВНЫЙ ЗАПРОС:
        $employee = Employee::moderatorLimit($answer)
        ->with('user', 'staffer')
        ->findOrFail($id);

        $user = $employee->user;
        $staffer = $employee->staffer;

        // Подключение политики
        $this->authorize(getmethod('update'), $employee);
        $this->authorize(getmethod('update'), $staffer);
        $this->authorize(getmethod('update'), $user);

        Log::info('Будем редактировать сотрудника: все необходимые права есть');

        // Отдаем работу по редактированию нового юзера трейту
        $user = $this->updateUser($request, $user);

        if($request->staff_id != null){

            // Перезаписываем данные
            $employee->employment_date = outPickMeUp($request->employment_date);
            $employee->dismissal_date = $request->dismissal_date == null ? null : outPickMeUp($request->dismissal_date);
            $employee->dismissal_description = $request->dismissal_description;
            $employee->editor_id = $request->user()->id;

            $employee->display = $request->display;
            $employee->system_item = $request->system_item;

            $employee->save();
        }

        // Если записалось
        if ($employee) {
            return redirect()->route('employees.index');
        } else {
            abort(403, 'Ошибка редактирования сотрудника');
        }

    }

    public function destroy($id)
    {
        //
    }
}
