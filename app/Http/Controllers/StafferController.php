<?php

namespace App\Http\Controllers;

// Подключаем модели
use App\Staffer;

use App\Employee;
use App\Page;
use App\User;
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
        $staff = $this->staffer->getIndex($answer, $request);

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
            return redirect()->action('DepartmentController@index', ['id' => $staffer->id, 'item' => 'staff']);
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
        $staffer = Staffer::with(['position', 'schedules.worktimes', 'employees' => function($query) {
            $query->whereNull('dismissal_date');
        }])
        ->moderatorLimit($answer)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $staffer);

        // Список пользователей
        $answer_users = operator_right('users', true, 'index');
        $user = $request->user();

        $users = User::moderatorLimit($answer_users)
        ->companiesLimit($answer_users)
        ->filials($answer_users) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
        ->authors($answer_users)
        ->systemItem($answer_users) // Фильтр по системным записям
        ->orderBy('second_name')
        ->get();

        $users_list = [];
        foreach ($users as $user) {
            $users_list[$user->id] = $user->second_name.' '.$user->first_name;
        }
        // dd($users_list);

        if (isset($staffer->schedules->first()->worktimes)) {
            $worktime_mass = $staffer->schedules->first()->worktimes->keyBy('weekday');
        }

        for($x = 1; $x<8; $x++){

            if(isset($worktime_mass[$x]->worktime_begin)){

                $worktime_begin = $worktime_mass[$x]->worktime_begin;
                $str_worktime_begin = secToTime($worktime_begin);
                $worktime[$x]['begin'] = $str_worktime_begin;

            } else {

                $worktime[$x]['begin'] = null;
            }

            if(isset($worktime_mass[$x]->worktime_interval)){

                $worktime_interval = $worktime_mass[$x]->worktime_interval;

                if(($worktime_begin + $worktime_interval) > 86400){

                    $str_worktime_interval = secToTime($worktime_begin + $worktime_interval - 86400);
                } else {

                    $str_worktime_interval = secToTime($worktime_begin + $worktime_interval);
                }

                $worktime[$x]['end'] = $str_worktime_interval;
            } else {

                $worktime[$x]['end'] = null;
            }

        }

        // Инфо о странице
        $page_info = pageInfo($this->entity_alias);

        return view('staff.edit', compact('staffer', 'page_info', 'worktime'));
    }

    public function update(EmployeeRequest $request, $id)
    {

        // dd($request);
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

        // dd(empty($request->user_id));
        // dd(isset($staffer->employee));

        if (isset($staffer->employee) || empty($request->user_id)) {
            $employee = $staffer->employee;
        } else {
            $staffer->employee()->create([
                'company_id' => $user->company_id,
                'user_id' => $request->user_id,
                'dismissal_date' => null,
                'author_id' => $user_id
            ]);
            $employee = $staffer->employee;
        }

        // Заполняем дату
        $employee->employment_date = outPickMeUp($request->employment_date);

        // Если пришла дата увольнения, то снимаем с должности в штате и удаляем роли
        if (isset($request->dismissal_date)) {

            $staffer->user_id = null;
            $employee->dismissal_date = outPickMeUp($request->dismissal_date);
            $employee->dismissal_description = $request->dismissal_description;

            $staffer->editor_id = $user_id;

            // Удаляем должность и права данного юзера
            $delete = RoleUser::where(['position_id' => $staffer->position_id, 'user_id' => $staffer->user_id])->delete();
        } else if (empty($staffer->employee) || isset($request->user_id)) {

            $staffer->user_id = $request->user_id;
            // Создать связь сотрудника, филиала и ролей должности
            $mass = [];

            foreach ($staffer->position->roles as $role) {
                $mass[] = [
                    'user_id' => $request->user_id,
                    'role_id' => $role->id,
                    'department_id' => $staffer->filial_id,
                    'position_id' => $staffer->position_id,
                    'author_id' => $user->id,
                ];
            }
            DB::table('role_user')->insert($mass);

        }

         // Расписание для штата
        setSchedule($request, $staffer);

        $employee->save();

        if ($employee) {
            $staffer->display = $request->display;

            $staffer->editor_id = $user_id;
            $staffer->save();

            if ($staffer) {
                return Redirect('/admin/staff');
            } else {
                abort(403, 'Ошибка при обновлении штата!');
            }
        } else {
            abort(403, 'Ошибка при обновлении сотрудника!');
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
        $user = $request->user();

        $staffer->editor_id = $user->id;
        $staffer->save();
        $staffer = Staffer::destroy($id);

        if ($staffer) {
            return redirect()->action('DepartmentController@index', ['id' => $department_id, 'item' => 'department']);
        } else {
            abort(403, 'Ошибка при удалении штата');
        }
    }


    // ---------------------------------------------- Ajax -----------------------------------------------------------

    // Сортировка
    public function ajax_sort(Request $request)
    {

        $i = 1;

        foreach ($request->staff as $item) {
            Staffer::where('id', $item)->update(['sort' => $i]);
            $i++;
        }
    }

    // Системная запись
    public function ajax_system_item(Request $request)
    {

        if ($request->action == 'lock') {
            $system = 1;
        } else {
            $system = null;
        }

        $item = Staffer::where('id', $request->id)->update(['system_item' => $system]);

        if ($item) {

            $result = [
                'error_status' => 0,
            ];
        } else {

            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при обновлении статуса системной записи!'
            ];
        }
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    // Отображение на сайте
    public function ajax_display(Request $request)
    {

        if ($request->action == 'hide') {
            $display = null;
        } else {
            $display = 1;
        }

        $item = Staffer::where('id', $request->id)->update(['display' => $display]);

        if ($item) {

            $result = [
                'error_status' => 0,
            ];
        } else {

            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при обновлении отображения на сайте!'
            ];
        }
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    // ---------------------------------------------- API --------------------------------------------------

    // Получаем вакансии по api
    public function api_index_vacancies (Request $request)
    {

        $site = Site::with(['company.staff.position', 'company.staff' => function ($query) {
            $query->whereNull('user_id');
        }])->where('api_token', $request->token)->first();
        if ($site) {
            // return Cache::remember('staff', 1, function() use ($domen) {
            return $site->company->staff;
            // });
        } else {
            return json_encode('Нет доступа, холмс!', JSON_UNESCAPED_UNICODE);
        }
    }

    // Получаем команду по api
    public function api_index_team (Request $request)
    {

        $site = Site::with(['company.staff.position', 'company.staff.user', 'company.staff' => function ($query) {
            $query->whereNotNull('user_id');
        }])->where('api_token', $request->token)->first();
        if ($site) {

            // return Cache::remember('staff', 1, function() use ($domen) {
            return $site->company->staff;
            // });
        } else {
            return json_encode('Нет доступа, холмс!', JSON_UNESCAPED_UNICODE);
        }
    }
}
