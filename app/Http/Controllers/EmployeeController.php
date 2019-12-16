<?php

namespace App\Http\Controllers;

use App\Employee;
use App\Http\Controllers\Traits\Photable;
use App\Staffer;
use App\User;

// Валидация
use Illuminate\Http\Request;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;

// Общие классы
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

	use Photable;

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
        }, 'user.main_phones'])
        ->moderatorLimit($answer)
        ->companiesLimit($answer)

        // Так как сущность не филиала зависимая, но по факту
        // все таки зависимая через staff, то делаем нестандартную фильтрацию (прямо в запросе)
        ->when($answer['dependence'] == true, function ($query) use ($user) {
            return $query->whereHas('staffer', function($q) use ($user){
                $q->where('filial_id', $user->staff->first()->filial_id);
            });
        })

        // Получаем только устроенных
        ->whereNull('dismissal_date')
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->booklistFilter($request)
        ->filter($request, 'position_id', 'staffer')
        ->filter($request, 'department_id', 'staffer')
        ->dateIntervalFilter($request, 'employment_date')

        ->whereHas('user', function($q) use ($request){
            $q->booleanArrayFilter($request, 'access_block');
        })

        ->whereHas('user', function($q) use ($request){
            $q->whereHas('location', function($q) use ($request){
                $q->filter($request, 'city_id');
            });
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
            'city',                 // Город
            'booklist'              // Списки пользователя
        ]);

        // Окончание фильтра -----------------------------------------------------------------------------------------

        // Дополнительные кнопки ------------------------------------
        $add_buttons = [];

        $dismissed_count = Employee::moderatorLimit($answer)->companiesLimit($answer)->whereNotNull('dismissal_date')
        ->authors($answer)
        ->systemItem($answer)
        ->count();

        $add_buttons[0]['href'] = '../admin/employees/dismissal';
        $add_buttons[0]['class'] = 'dismissed';
        $add_buttons[0]['text'] = 'Уволенные сотрудники: ' . $dismissed_count;

        $add_buttons[1]['href'] = '../admin/departments';
        $add_buttons[1]['class'] = 'alert';
        $add_buttons[1]['text'] = 'Структура';

        $add_buttons = collect($add_buttons);
        // -----------------------------------------------------------

        // Инфо о странице
        $page_info = pageInfo($this->entity_alias);

        return view('employees.index', compact('employees', 'page_info', 'filter', 'add_buttons'));
    }

    // Отдельный метод на базе index для того чтобы показывать только уволенных
    public function dismissal(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod('index'), $this->class);

        // Включение контроля активного фильтра
        $filter_url = autoFilter($request, $this->entity_alias);
        if(($filter_url != null)&&($request->filter != 'active')){return Redirect($filter_url);};

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod('index'));

        // Смотрим сколько филиалов в компании
        $user = $request->user();


        // // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        // $answer = operator_right('staff', true, getmethod('index'));

        // $staff = Staffer::moderatorLimit($answer)
        // ->companiesLimit($answer)
        // ->filials($answer)
        // ->authors($answer)
        // ->systemItem($answer)
        // ->get();

        // $staff_id_mass = $staff->pluck('id')->toArray();


        // -------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -------------------------------------------------------------------------------------------
        $employees = Employee::with(['company.filials', 'staffer' => function($q) {
            $q->with('position', 'filial', 'department');
        }, 'user.main_phones'])
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        // ->whereIn('user_id', $staff_id_mass)

        // Так как сущность не филиала зависимая, но по факту
        // все таки зависимая через staff, то делаем нестандартную фильтрацию (прямо в запросе)
        // ->when($answer['dependence'] == true, function ($query) use ($user) {
        //     return $query->whereHas('staffer', function($q) use ($user){
        //         $q->where('filial_id', $user->filial_id);
        //     });
        // })

        // Получаем только уволенных
        ->whereNotNull('dismissal_date')

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

        // Дополнительные кнопки ------------------------------------
        $add_buttons = [];

        $add_buttons[0]['href'] = '/admin/employees';
        $add_buttons[0]['class'] = 'dismissed';
        $add_buttons[0]['text'] = 'Действующие сотрудники';

        $add_buttons = collect($add_buttons);
        // -----------------------------------------------------------

        // Инфо о странице
        $page_info = pageInfo($this->entity_alias);

        $page_info->title = 'Уволенные сотрудники';
        $page_info->name = 'Уволенные сотрудники';
        return view('employees.dismissal', compact('employees', 'page_info', 'filter', 'add_buttons'));
    }


    public function create()
    {
        //Подключение политики
        $this->authorize(getmethod('create'), Employee::class);
        // $this->authorize(getmethod('create'), User::class);

        // Создаем новый экземляр дилера
        $employee = new Employee;

        // Создаем новый экземляр пользователя
        $user = new User;

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $list_empty_staff = Staffer::moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer)
        ->whereNull('user_id')
        ->get();

        $list_user_employees = Employee::with('user')
        ->moderatorLimit($answer)
        ->where('user_id', $employee->user_id)
        ->get();

        // Инфо о странице
        $page_info = pageInfo($this->entity_alias);

        $auth_user = \Auth::user();

        return view('employees.create', compact('user', 'employee', 'page_info', 'list_empty_staff', 'list_user_employees', 'auth_user'));
    }

    public function store(UserStoreRequest $request)
    {
        // Подключение политики
        $this->authorize(getmethod('create'), Employee::class);

        Log::info('Будем создавать сотрудника: все необходимые права есть');

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // Получаем данные для авторизованного пользователя
        $user_auth = $request->user();

        // Скрываем бога
        $user_id = hideGod($user_auth);

        $staff = Staffer::with('position', 'department')->findOrFail($request->staff_id);

        // Отдаем работу по созданию нового юзера трейту
        $user = $this->createUser($request, 1);
        $employment_date = $request->employment_date;

        $this->employment($user, $employment_date, $staff);

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
        $employee = Employee::with('user.photo', 'staffer')
        ->companiesLimit($answer)
        ->filials($answer) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->moderatorLimit($answer)
        ->findOrFail($id);

        $list_user_employees = Employee::with('user')
        ->moderatorLimit($answer)
        ->where('user_id', $employee->user_id)
        ->get();

        $list_empty_staff = Staffer::moderatorLimit($answer)
        ->whereNull('user_id')
        ->get();

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $employee);
        // $this->authorize(getmethod(__FUNCTION__), $employee->user);

        // Инфо о странице
        $page_info = pageInfo($this->entity_alias);

        $user = $employee->user;


        return view('employees.edit', compact('employee', 'page_info', 'list_user_employees', 'list_empty_staff'));
    }


    public function update(UserUpdateRequest $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias,  $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $employee = Employee::moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer)
        ->with('user', 'staffer')
        ->findOrFail($id);

        $user = $employee->user;
        $staffer = $employee->staffer;

        // Подключение политики
        $this->authorize(getmethod('update'), $employee);
        // $this->authorize(getmethod('update'), $staffer);
        // $this->authorize(getmethod('update'), $user);

        Log::info('Будем редактировать сотрудника: все необходимые права есть');

        // Отдаем работу по редактированию нового юзера трейту
        $user = $this->updateUser($request, $user);


        $photo_id = $this->getPhotoId($request, $user);
        $user->photo_id = $photo_id;
        $user->save();

        if($request->staff_id != null){

            if($employee->staffer->id == $request->staff_id){

            } else {

                // Будем увольнять с текущей должности и устраивать на новую


            }

            // Перезаписываем данные
            $employee->employment_date = outPickMeUp($request->employment_date);
            $employee->dismissal_date = $request->dismissal_date == null ? null : outPickMeUp($request->dismissal_date);
            $employee->dismissal_description = $request->dismissal_description;
            $employee->editor_id = $request->user()->id;

            $employee->display = $request->display;
            $employee->system = $request->system;

            $employee->save();


            // Если сотрудник удачно отредактирован - занимем ставку
            if($employee){

                // Проверяем: свободна ли ставка  =====================================================
                Log::info('Проверяем: свободна ли ставка?');

                $staff = Staffer::with('position.roles')->findOrFail($request->staff_id);
                // dd($staff);

                if($staff->user_id != null){
                    abort(403, "Ставка не свободна!");
                } else {
                    Log::info('Ставка свободна!');
                };


                Log::info('Сотрудник создан - будем занимать ставку!');
                $staff->user_id = $user->id;
                $staff->save();

                // Прописываем права
                $position = $staff->position;
                $position->load('roles');

                $roles = [];
                foreach ($position->roles as $role) {
                    $insert_array[$role->id] = [
                        'department_id' => $staff->department_id,
                        'position_id' => $staff->position_id
                    ];
                }

                $user->roles()->attach($roles);

                $notifications = [];
                foreach ($position->notifications as $notification) {
                    $notifications[] = $notification->id;
                }
                $user->notifications()->attach($notifications);

                Log::info('Записали роли для юзера (сотрудника)');


            } else {

                Log::info('Сотрудник не был создан!');
            };



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

    public function ajax_employee_dismiss_modal(Request $request)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias,  $this->entity_dependence, 'update');

        // ГЛАВНЫЙ ЗАПРОС:
        $employee = Employee::with('user', 'staffer')->moderatorLimit($answer)->findOrFail($request->employee_id);

        // Подключение политики
        $this->authorize(getmethod('update'), $employee);

        return view('employees.modals.dismiss', compact('employee'));
    }


    public function ajax_employee_dismiss(Request $request)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias,  $this->entity_dependence, 'update');

        // ГЛАВНЫЙ ЗАПРОС:
        $employee = Employee::with('user', 'staffer')->moderatorLimit($answer)->findOrFail($request->employee_id);

        $this->dismiss($employee, $request->dismissal_date, $request->dismissal_description);

        return $employee;

    }


    public function ajax_employee_employment_modal(Request $request)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_user = operator_right('users',  true, 'index');

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_staff = operator_right('staff',  true, 'index');

        // ГЛАВНЫЙ ЗАПРОС:
        $user = User::moderatorLimit($answer_user)->findOrFail($request->user_id);

        $list_empty_staff = Staffer::moderatorLimit($answer_staff)->whereNull('user_id')->get();

        return view('employees.modals.employment', compact('user', 'list_empty_staff'));
    }

    public function ajax_employee_employment(Request $request)
    {


        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_user = operator_right('users',  true, 'index');

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        // $answer_staff = operator_right('staff',  true, 'index');

        // ГЛАВНЫЙ ЗАПРОС:
        $user = User::moderatorLimit($answer_user)->findOrFail($request->user_id);
        $staff = Staffer::with('position', 'department')->findOrFail($request->staff_id);

        $new_employee = $this->employment($user, $request->employment_date, $staff);
        Log::info('Устроили нового сотрудника');

        return $new_employee;

    }


    // Функция увольнения сотрудника
    public function dismiss($employee, $dismissal_date, $dismissal_description)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias,  $this->entity_dependence, 'update');

        $staff = $employee->staffer;
        $user = $employee->user;

        // Подключение политики
        $this->authorize(getmethod('update'), $employee);
        $this->authorize(getmethod('update'), $staff);
        $this->authorize(getmethod('update'), $user);

        $employee->dismissal_date = outPickMeUp($dismissal_date);
        $employee->dismissal_description = $dismissal_description;
        $employee->save();

        Log::info('Освобождаем должность');
        $staff->user_id = null;
        $staff->save();

        Log::info('Блокируем пользователя');
        $user->access_block = 1;
        $user->save();

        Log::info('Удаляем все его роли');
        $user->roles()->detach();

        return true;

    }


    // Функция трудоустройства пользователя
    public function employment($user, $employment_date, $staff)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias,  $this->entity_dependence, 'create');

        // Подключение политики
        $this->authorize(getmethod('create'), Employee::class);
        $this->authorize(getmethod('update'), $staff);
        $this->authorize(getmethod('update'), $user);

        $employee = new Employee;

        $employee->employment_date = outPickMeUp($employment_date);
        $employee->user_id = $user->id;
        $employee->staffer_id = $staff->id;

        $employee->company_id = $user->company->id;
        $employee->author_id = $user->id;

        $employee->display = 1;
        $employee->system = 0;

        $employee->save();

        Log::info('Занимаем должность');

        // Проверяем: свободна ли ставка  =====================================================
        Log::info('Проверяем: свободна ли ставка?');

        if($staff->user_id != null){abort(403, "Ставка не свободна!");} else {Log::info('Ставка свободна!');};

        $staff->user_id = $employee->user_id;
        $staff->save();

        Log::info('Открываем доступ для пользователя');
        $user->access_block = 0;

        Log::info('Определяем пользователя как созданного под филиалом должности');
        $user->filial_id = $staff->filial_id;

        $user->save();

        Log::info('Формируем права');
        setRolesFromPosition($staff->position, $staff->department, $user);

        return $employee;

    }


}
