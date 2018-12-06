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

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        $staffer = new $this->class;

        $staffer->company_id = $user->company_id;
        $staffer->author_id = hideGod($user);

        // Системная запись
        $staffer->system_item = $request->system_item;
        $staffer->display = $request->display;

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // Если нет прав на создание полноценной записи - запись отправляем на модерацию
        if ($answer['automoderate'] == false){
            $staffer->moderation = 1;
        }

        $staffer->position_id = $request->position_id;
        $staffer->department_id = $request->department_id;
        $staffer->filial_id = $request->filial_id;

        $staffer->save();

        if ($staffer) {

        // Переадресовываем на index
            return redirect()->route('departments.index', ['id' => $staffer->id, 'item' => 'staff']);
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
        $staffer = Staffer::with('schedules.worktimes', 'position', 'employee')->moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $staffer);

        // Если на должность назначен сотрудник
        if (isset($staffer->employee)) {

            $employee = $staffer->employee;
            $employment_date = outPickMeUp($request->employment_date);

            if ($employee->employment_date != $employment_date) {
                $employee->employment_date = $employment_date;
                $employee->editor_id = $user_id;
                $employee->save();
            }

        } else {

            $staffer->employees()->create([
                'company_id' => $user->company_id,
                'user_id' => $request->user_id,
                'employment_date' => outPickMeUp($request->employment_date),
                'dismissal_date' => null,
                'author_id' => $user_id,
            ]);

            $employee = $staffer->load('employee');
            $employee = $staffer->employee;

            $staffer->user_id = $request->user_id;

            $roles = [];
            foreach ($staffer->position->roles as $role) {
                $roles[$role->id] = [
                    'department_id' => $staffer->filial_id,
                    'position_id' => $staffer->position_id,
                    'author_id' => $user_id,
                ];
            }

            User::findOrFail($request->user_id)->roles()->attach($roles);

        }
        // dd($employee);
        // dd($staffer);

        // Если пришла дата увольнения, то снимаем с должности в штате и удаляем роли
        if (isset($request->dismissal_date)) {

            $employee->dismissal_date = outPickMeUp($request->dismissal_date);
            $employee->dismissal_description = $request->dismissal_description;
            $employee->editor_id = $user_id;
            $employee->save();

            // Удаляем должность и права данного юзера
            // 05.12.18 - решали что при увольнении сносить все права (включая спецправа), т.к. должность одна пока что
            User::findOrFail($staffer->user_id)->roles()->detach();

            // Освобождаем штат
            $staffer->user_id = null;
            $staffer->editor_id = $user_id;

        }

        // Расписание для штата
        setSchedule($request, $staffer);

        $staffer->display = $request->display;
        $staffer->editor_id = $user_id;
        $staffer->save();

        if ($staffer) {
            return redirect()->route('staff.index');
        } else {
            abort(403, 'Ошибка при обновлении штата!');
        }
    }

    public function destroy(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, true, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $staffer = Staffer::with('department')->moderatorLimit($answer)->findOrFail($id);
        $department_id = $staffer->department_id;

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $staffer);

        // Удаляем должность из отдела с обновлением
        // Находим филиал и отдел
        $staffer->editor_id = $request->user()->id;
        $staffer->save();
        $staffer = Staffer::destroy($id);

        if ($staffer) {
            return redirect()->route('departments.index', ['id' => $department_id, 'item' => 'department']);
        } else {
            abort(403, 'Ошибка при удалении штата');
        }
    }
}
