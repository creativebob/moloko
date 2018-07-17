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

// Политика
use App\Policies\StafferPolicy;

// Подключаем фасады
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StafferController extends Controller
{
  // Сущность над которой производит операции контроллер
  protected $entity_name = 'staff';
  protected $entity_dependence = true;

  public function index(Request $request)
  {

    // Подключение политики
    $this->authorize(getmethod(__FUNCTION__), Staffer::class);
    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

    // -------------------------------------------------------------------------------------------
    // ГЛАВНЫЙ ЗАПРОС
    // -------------------------------------------------------------------------------------------

    $staff = Staffer::with('filial', 'department', 'user', 'position', 'employees')
    ->moderatorLimit($answer)
    ->companiesLimit($answer)
    ->filials($answer) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
    ->authors($answer)
    ->systemItem($answer) // Фильтр по системным записям
    ->booklistFilter($request)
    ->filter($request, 'position_id')
    ->filter($request, 'department_id')
    ->dateIntervalFilter($request, 'date_employment')
    ->orderBy('moderation', 'desc')
    ->paginate(30);
    

    // ---------------------------------------------------------------------------------------------------------------------------------------------
    // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ----------------------------------------------------------------------------------------------------------------
    // ---------------------------------------------------------------------------------------------------------------------------------------------

    $filter_query = Staffer::moderatorLimit($answer)
    ->companiesLimit($answer)
    ->filials($answer) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
    ->authors($answer)
    ->systemItem($answer) // Фильтр по системным записям
    ->get();

    // dd($filter_query);
    $filter['status'] = null;

    $filter = addFilter($filter, $filter_query, $request, 'Выберите должность:', 'position', 'position_id');
    $filter = addFilter($filter, $filter_query, $request, 'Выберите отдел:', 'department', 'department_id');

    // Добавляем данные по спискам (Требуется на каждом контроллере)
    $filter = addBooklist($filter, $filter_query, $request, $this->entity_name);
    // dd($filter);

    // ---------------------------------------------------------------------------------------------------------------------------------------------

    $user = $request->user();
    // Смотрим сколько филиалов в компании
    $company = Company::with(['departments' => function($query) {

      $query->whereFilial_status(1);
    }])->findOrFail($user->company_id);

    $filials = count($company->departments);
    // dd($staff);

    // Инфо о странице
    $page_info = pageInfo($this->entity_name);    

    return view('staff.index', compact('staff', 'page_info', 'filials', 'filter'));
  }

  public function create()
  {
    return redirect()->action('DepartmentController@index');
  }

  public function store(StafferRequest $request)
  {

    // Подключение политики
    $this->authorize(getmethod(__FUNCTION__), Staffer::class);

    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

    // Получаем данные для авторизованного пользователя
    $user = $request->user();
    $company_id = $user->company_id;

    if ($user->god == 1) {
      $user_id = 1;
    } else {
      $user_id = $user->id;
    };

    // Пишем вакансию в бд
    $position_id = $request->position_id;
    $department_id = $request->department_id;
    $filial_id = $request->filial_id;

    $staffer = new Staffer;
    // Пишем ID компании авторизованного пользователя
    if ($company_id == null) {
      abort(403, 'Необходимо авторизоваться под компанией');
    };

    $staffer->display = $request->display;

    $staffer->company_id = $company_id;
    $staffer->position_id = $request->position_id;
    $staffer->department_id = $request->department_id;
    $staffer->filial_id = $request->filial_id;
    $staffer->author_id = $user_id;
    $staffer->save();

    if ($staffer) {
      // Переадресовываем на index
      return redirect()->action('DepartmentController@index', ['id' => $staffer->id, 'item' => 'staffer']);
    } else {
      abort(403, 'Ошибка при записи штата!');
    };
  }

  public function show($id)
  {
      //
  }

  public function edit(Request $request, $id)
  {

    $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));
    
    // ГЛАВНЫЙ ЗАПРОС:
    $staffer = Staffer::with(['position', 'schedules.worktimes', 'employees' => function($query) {
      $query->wheredismissal_date(null);
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
    };
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
    $page_info = pageInfo($this->entity_name);

    return view('staff.edit', compact('staffer', 'users_list', 'page_info', 'worktime'));    
  }

  public function update(EmployeeRequest $request, $id)
  {

    // Получаем авторизованного пользователя
    $user = $request->user();

    // Скрываем бога
    $user_id = hideGod($user);

    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, true, getmethod(__FUNCTION__));

    // ГЛАВНЫЙ ЗАПРОС:
    $staffer = Staffer::with('schedules.worktimes', 'position')->moderatorLimit($answer)->findOrFail($id);

    // Подключение политики
    $this->authorize(getmethod(__FUNCTION__), $staffer);

    // Если не существует расписания для компании - создаем его
    if($staffer->schedules->count() < 1){

      $schedule = new Schedule;
      $schedule->company_id = $user->company_id;
      $schedule->name = 'График работы для должности: ' . $staffer->position->name;
      $schedule->description = null;
      $schedule->author_id = $user_id;
      $schedule->save();

            // Создаем связь расписания с компанией
      $schedule_entity = new ScheduleEntity;
      $schedule_entity->schedule_id = $schedule->id;
      $schedule_entity->entity_id = $staffer->id;
      $schedule_entity->entity = 'staff';
      $schedule_entity->save();

      $schedule_id = $schedule->id;
    } else {

      $schedule_id = $staffer->schedules->first()->id;
    }

        // Функция getWorktimes ловит все поля расписания из запроса и готовит к записи в worktimes
    $mass_time = getWorktimes($request, $schedule_id);

        // Удаляем все записи времени в worktimes для этого расписания
    $worktimes = Worktime::where('schedule_id', $schedule_id)->forceDelete();

        // Вставляем новое время в расписание
    DB::table('worktimes')->insert($mass_time);

    // Если не пустая дата увольнения пришла
    if (isset($request->dismissal_date)) {
      // Снимаем с должности в штате
      $staffer->user_id = null;
      $staffer->editor_id = $user->id;

      // Ищем в сотрудниках по id должности и где пустая дата увольнения
      $employee = Employee::where(['staffer_id' => $id, 'dismissal_date' => null])->first();
      // Заполняем дату
      $employee->employment_date = $request->employment_date;
      $employee->dismissal_date = $request->dismissal_date;
      $employee->dismissal_description = $request->dismissal_description;
      $employee->editor_id = $user->id;

      // Удаляем должность и права данного юзера
      $delete = RoleUser::where(['position_id' => $staffer->position_id, 'user_id' => $staffer->user_id])->delete();

    // Если даты увольнения нет
    } else {
      $user_id = $staffer->user_id;
      $employee = Employee::where(['staffer_id' => $id, 'user_id' => $user_id, 'dismissal_date' => null])->first();
      if ($employee) {
        $employment_date_db = $employee->employment_date;
        // Смотрим отличатеся ли пришедшая дата устройства
        if ($employment_date_db !== $request->employment_date) {
          $employee->employment_date = $request->employment_date;
          $employee->save();
          if ($employee) {
            return Redirect('/admin/staff');
          } else {
            abort(403, 'Ошибка при записи даты приема на должность!');
          }
        }
      } else {
        // Назначаем пользователя
        $staffer->user_id = $request->user_id;

        
        // Создаем новую запись в сотрудниках
        $employee = new Employee;
        $employee->company_id = $user->company_id;
        $employee->staffer_id = $id;
        $employee->user_id = $request->user_id;
        $employee->employment_date = $request->employment_date;
        $employee->author_id = $user->id;
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
    } 
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
    $answer = operator_right($this->entity_name, true, getmethod(__FUNCTION__));
    
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
  // Отображение на сайте
  public function ajax_display(Request $request)
  {

    if ($request->action == 'hide') {
      $display = null;
    } else {
      $display = 1;
    }

    $staffer = Staffer::findOrFail($request->id);
    $staffer->display = $display;
    $staffer->save();

    if ($staffer) {

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

  // Сортировка
  public function staff_sort(Request $request)
  {
    $result = '';
    $i = 1;
    foreach ($request->staff as $item) {

      $staff = Staffer::findOrFail($item);
      $staff->sort = $i;
      $staff->save();
      $i++;
    }
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
