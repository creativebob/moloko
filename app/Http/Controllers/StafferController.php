<?php

namespace App\Http\Controllers;

// Подключаем модели
use App\Staffer;
use App\Employee;
use App\User;

use App\Page;
use App\Site;
use App\Company;
use App\Department;
use App\RoleUser;
use App\Worktime;
use App\ScheduleEntity;
use App\Schedule;

// Валидация
use App\Http\Requests\StafferRequest;
use App\Http\Requests\EmployeeRequest;

// Подключаем фасады
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StafferController extends Controller
{

    // Настройки сконтроллера
    public function __construct(Staffer $staffer)
    {
        $this->middleware('auth');
        $this->staffer = $staffer;
        $this->class = Staffer::class;
        $this->model = 'App\Staffer';
        $this->entity_alias = with(new $this->class)->getTable();
        $this->entity_dependence = true;
    }


    public function index(Request $request)
    {

        // Включение контроля активного фильтра
        $filter_url = autoFilter($request, $this->entity_alias);
        if(($filter_url != null)&&($request->filter != 'active')){return Redirect($filter_url);};

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // dd($answer);

        // -------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -------------------------------------------------------------------------------------------
        $staff = $this->staffer->getIndex($request, $answer);
        // dd($staff);

        // -----------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ------------------------------------------------------------------------------
        // -----------------------------------------------------------------------------------------------------------

        $filter = setFilter($this->entity_alias, $request, [
            'position',             // Должность
            'department',           // Отдел
            'date_interval',        // Дата
            'booklist'              // Списки пользователя
        ]);

        // Окончание фильтра -----------------------------------------------------------------------------------------

        // dd($staff);

        // Инфо о странице
        $page_info = pageInfo($this->entity_alias);

        return view('staff.index', compact('staff', 'page_info', 'filter'));
    }

    public function create()
    {
        return redirect()->action('DepartmentController@index');
    }

    public function store(StafferRequest $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        $data = $request->input();
        $data['department_id'] = $data['parent_id'];
        $staffer = Staffer::create($data);

        if ($staffer) {

        // Переадресовываем на index
            return redirect()->route('departments.index', [
                'id' => $staffer->id,
                'item' => $this->entity_alias
            ]);
        } else {
            abort(403, 'Ошибка при записи штата!');
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

        // ГЛАВНЫЙ ЗАПРОС:
        $staffer = Staffer::with(['position', 'schedules.worktimes', 'employee'])
        ->moderatorLimit($answer)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $staffer);

        // Инфо о странице
        $page_info = pageInfo($this->entity_alias);

        return view('staff.edit', compact('staffer', 'page_info'));
    }

    public function update(EmployeeRequest $request, $id)
    {

        // Получаем авторизованного пользователя
        $user = $request->user();

        // Скрываем бога
        $user_id = hideGod($user);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, true, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $staffer = Staffer::with([
            'schedules.worktimes',
            'position' => function($q) {
                $q->with([
                   'roles',
                   'notifications'
                ]);
            },
            'employee'
        ])
            ->moderatorLimit($answer)
            ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $staffer);

        Log::channel('personals')
            ->info("============== ОБНОВЛЕНИЕ {$staffer->getTable()} с id: {$staffer->id} ========");

        // Если на должность назначен сотрудник
        if (isset($staffer->employee)) {

            $employee = $staffer->employee;
            $employment_date = outPickMeUp($request->employment_date);

            if ($employee->employment_date != $employment_date) {
                $employee->employment_date = $employment_date;
                $employee->editor_id = $user_id;
                $employee->save();
            }

            Log::channel('personals')
                ->info("На staffer: {$staffer->id} назнанчен сотрудник {$user->name}");

        } else {

            $staffer->employees()->create([
                'user_id' => $request->user_id,
                'employment_date' => outPickMeUp($request->employment_date),
                'dismissal_date' => null,
            ]);

            $staffer->load('employee');
            $employee = $staffer->employee;

            $staffer->user_id = $request->user_id;

            $roles = [];
            foreach ($staffer->position->roles as $role) {
                $roles[$role->id] = [
                    'department_id' => $staffer->filial_id,
                    'position_id' => $staffer->position_id,
                ];
            }

            $user = User::findOrFail($request->user_id);

            $user->roles()->attach($roles);

            $notifications = [];
            foreach ($staffer->position->notifications as $notification) {
                $notifications[] = $notification->id;
            }
//            dd($notifications);
            $user->notifications()->attach($notifications);

            Log::channel('personals')
                ->info("На staffer: {$staffer->id} назнанчен сотрудник {$user->name}");

        }


        // dd($employee);
        // dd($staffer);

        // Если пришла дата увольнения, то снимаем с должности в штате и удаляем роли
        if (isset($request->dismissal_date)) {


            $employee->dismissal_date = outPickMeUp($request->dismissal_date);
            $employee->dismissal_description = $request->dismissal_description;
            $employee->save();


            // Удаляем должность и права данного юзера
            // TODO - 05.12.18 - решали что при увольнении сносить все права (включая спецправа), т.к. должность одна пока что
            $user = User::findOrFail($staffer->user_id);
            $res = $user->roles()->detach();
            $res = $user->notifications()->detach();

            // Освобождаем штат
            $staffer->user_id = null;

            Log::channel('personals')
                ->info("C staffer: {$staffer->id} уволен сотрудник {$user->name}");

        }

        // Расписание для штата
        setSchedule($request, $staffer);

        $staffer->save();

        Log::channel('personals')
            ->info("============== ЗАВЕРШЕНО ОБНОВЛЕНИЕ {$staffer->getTable()} с id: {$staffer->id} ========
            
            ");

        if ($staffer) {
            return redirect()->route('staff.index');
        } else {
            abort(403, 'Ошибка при обновлении штата!');
        }
    }

    public function destroy(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $staffer = Staffer::with('user')->moderatorLimit($answer)->findOrFail($id);
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $staffer);

        // Удаляем должность из отдела с обновлением
        // Находим филиал и отдел
        $staffer->editor_id = hideGod($request->user());
        $staffer->save();

        $parent_id = $staffer->department_id;

        $staffer = Staffer::destroy($id);

        if ($staffer) {
            return redirect()->route('departments.index', ['id' => $parent_id, 'item' => 'departments']);
        } else {
            abort(403, 'Ошибка при удалении штата');
        }
    }
}
