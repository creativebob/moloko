<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\UserControllerTrait;
use App\Http\Controllers\Traits\Photable;
use App\Http\Requests\System\EmployeeRequest;
use App\Staffer;
use App\User;
use App\Employee;
use Illuminate\Http\Request;
use App\Http\Requests\System\UserStoreRequest;
use App\Http\Requests\System\UserUpdateRequest;

class EmployeeController extends Controller
{

    /**
     * EmployeeController constructor.
     * @param Employee $employee
     */
    public function __construct(Employee $employee)
    {
        $this->middleware('auth');
        $this->employee = $employee;
        $this->class = Employee::class;
        $this->model = 'App\Employee';
        $this->entity_alias = with(new $this->class)->getTable();
        $this->entity_dependence = true;
    }

	use Photable;
    // Подключаем трейт записи и обновления компании
    use UserControllerTrait;

    /**
     * Отображение списка ресурсов
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
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
        $employees = Employee::with([
            'company.filials',
            'staffer' => function($q) {
                $q->with([
                    'position',
                    'filial',
                    'department'
                ]);
            },
            'user.main_phones'
        ])
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
//        dd($employees);

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

        // Инфо о странице
        $pageInfo = pageInfo($this->entity_alias);

        return view('system.pages.hr.employees.index', compact('employees', 'pageInfo', 'filter'));
    }

    /**
     * Показать форму для создания нового ресурса
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create()
    {
        //Подключение политики
        $this->authorize(getmethod('create'), Employee::class);
        $this->authorize(getmethod('create'), User::class);

        // Создаем новый экземляр дилера
        $employee = Employee::make();

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
        $pageInfo = pageInfo($this->entity_alias);

        $auth_user = \Auth::user();

        return view('system.pages.hr.employees.create', compact('user', 'employee', 'pageInfo', 'list_empty_staff', 'list_user_employees', 'auth_user'));
    }

    /**
     * Сохранение только что созданного ресурса в хранилище
     *
     * @param UserStoreRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(EmployeeRequest $request, UserStoreRequest $request_user)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        logs('hr')->info('Будем создавать сотрудника: все необходимые права есть');

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // Получаем данные для авторизованного пользователя
        $user_auth = auth()->user();

        // Скрываем бога
        $user_id = hideGod($user_auth);

        $staff = Staffer::with([
            'position',
            'department'
        ])->findOrFail($request_user->staffer_id);

        logs('hr')
            ->info("Занимаем должность");

        // Проверяем: свободна ли ставка  =====================================================
        logs('hr')->info('Проверяем: свободна ли ставка?');

        if ($staff->user_id != null) {
            abort(403, "Ставка не свободна!");
        } else {
            logs('hr')->info('Ставка свободна!');
        };

        // Отдаем работу по созданию нового юзера трейту, с указанием id сайта для привязки
        $user = $this->createUser($request_user, 1);

        logs('hr')
            ->info("Cоздаем сотрудника");

        $data = $request->validated();
        $data['user_id'] = $user->id;
        $employee = Employee::create($data);



        $staff->update([
            'user_id' => $user->id
        ]);

        $this->employment($user, $employee, $staff);

        return redirect()->route('employees.index');
    }

    /**
     * Отображение указанного ресурса
     *
     * @param $id
     */
    public function show($id)
    {
        //
    }

    /**
     * Показать форму для редактирования указанного ресурса
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias,  $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $employee = Employee::with([
            'user.photo',
            'staffer'
        ])
        ->companiesLimit($answer)
        ->filials($answer)
        ->authors($answer)
        ->systemItem($answer)
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
        $this->authorize(getmethod(__FUNCTION__), $employee->user);

        // Инфо о странице
        $pageInfo = pageInfo($this->entity_alias);

        return view('system.pages.hr.employees.edit', compact('employee', 'pageInfo', 'list_user_employees', 'list_empty_staff'));
    }

    /**
     * Обновление указанного ресурса в хранилище
     *
     * @param UserUpdateRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
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
        $this->authorize(getmethod('update'), $staffer);
        $this->authorize(getmethod('update'), $user);

        logs('hr')->info('Будем редактировать сотрудника: все необходимые права есть');

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
                logs('hr')->info('Проверяем: свободна ли ставка?');

                $staff = Staffer::with('position.roles')->findOrFail($request->staff_id);
                // dd($staff);

                if($staff->user_id != null){
                    abort(403, "Ставка не свободна!");
                } else {
                    logs('hr')->info('Ставка свободна!');
                };


                logs('hr')->info('Сотрудник создан - будем занимать ставку!');
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

                logs('hr')->info('Записали роли для юзера (сотрудника)');


            } else {

                logs('hr')->info('Сотрудник не был создан!');
            };



        }

        // Если записалось
        if ($employee) {
            return redirect()->route('employees.index');
        } else {
            abort(403, 'Ошибка редактирования сотрудника');
        }

    }

    /**
     * Удаление указанного ресурса из хранилища
     *
     * @param $id
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Отображение списка уволенных сотрудников
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
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
        $employees = Employee::with([
                'company.filials', 'staffer' => function($q) {
                    $q->with([
                        'position',
                        'filial',
                        'department'
                    ]);
                }, 'user.main_phones'
            ])
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

        // -----------------------------------------------------------

        // Инфо о странице
        $pageInfo = pageInfo($this->entity_alias);

        $pageInfo->title = 'Уволенные сотрудники';
        $pageInfo->name = 'Уволенные сотрудники';

        return view('system.pages.hr.employees.dismissal', compact('employees', 'pageInfo', 'filter'));
    }

    public function ajax_employee_dismiss_modal(Request $request)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias,  $this->entity_dependence, 'update');

        // ГЛАВНЫЙ ЗАПРОС:
        $employee = Employee::with('user', 'staffer')->moderatorLimit($answer)->findOrFail($request->employee_id);

        // Подключение политики
        $this->authorize(getmethod('update'), $employee);

        return view('system.pages.hr.employees.modals.dismiss', compact('employee'));
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

        return view('system.pages.hr.employees.modals.employment', compact('user', 'list_empty_staff'));
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
        logs('hr')->info('Устроили нового сотрудника');

        return $new_employee;

    }

    // Функция трудоустройства пользователя
    public function employment($user, $employee, $staff)
    {

        // Подключение политики
        $this->authorize(getmethod('create'), Employee::class);
        $this->authorize(getmethod('update'), $staff);
        $this->authorize(getmethod('update'), $user);




        logs('hr')->info('Открываем доступ для пользователя');
        $user->access_block = 0;

        logs('hr')->info('Определяем пользователя как созданного под филиалом должности');
        $user->filial_id = $staff->filial_id;

        $user->save();

        logs('hr')->info('Формируем права');
        setRolesFromPosition($staff->position, $staff->department, $user);

        return $employee;

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

        logs('hr')->info('Освобождаем должность');
        $staff->user_id = null;
        $staff->save();

        logs('hr')->info('Блокируем пользователя');
        $user->access_block = 1;
        $user->save();

        logs('hr')->info('Удаляем все его роли');
        $user->roles()->detach();

        return true;

    }
}
