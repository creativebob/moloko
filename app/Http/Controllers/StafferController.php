<?php

namespace App\Http\Controllers;

// Подключаем модели
use App\Staffer;
use App\Employee;
use App\Page;
use App\User;
use App\Company;
use App\Department;
use App\RoleUser;

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
  protected $entity_dependence = false;

  public function index(Request $request)
  {
    // Получаем метод
    $method = __FUNCTION__;
    // Подключение политики
    $this->authorize($method, Staffer::class);
    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_dependence, $method);
    // -------------------------------------------------------------------------------------------
    // ГЛАВНЫЙ ЗАПРОС
    // -------------------------------------------------------------------------------------------
    $staff = Staffer::with('filial', 'department', 'user', 'position', 'employees')
    ->withoutGlobalScope($answer['moderator'])
    ->moderatorFilter($answer)
    ->companiesFilter($answer)
    ->filials($answer) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
    ->authors($answer)
    ->systemItem($answer) // Фильтр по системным записям
    ->orderBy('moderated', 'desc')
    ->paginate(30);
    
    $user = $request->user();
    // Смотрим сколько филиалов в компании
    $company = Company::with(['departments' => function($query) {
                  $query->whereFilial_status(1);
                }])->findOrFail($user->company_id);
    $filials = count($company->departments);
    // dd($staff);
    // Инфо о странице
    $page_info = pageInfo($this->entity_name);     
    return view('staff.index', compact('staff', 'page_info', 'filials'));
  }

  public function create()
  {
      //
  }

  public function store(StafferRequest $request)
  {
    // Получаем метод
    $method = 'create';
    // Подключение политики
    $this->authorize($method, Staffer::class);
    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_dependence, $method);

    // Получаем данные для авторизованного пользователя
    $user = $request->user();
    $user_id = $user->id;
    $user_status = $user->god;
    $company_id = $user->company_id;

    // Пишем вакансию в бд
    $position_id = $request->position_id;
    $department_id = $request->parent_id;
    $filial_id = $request->filial_id;

    $staffer = new Staffer;
    // Пишем ID компании авторизованного пользователя
    if ($user->company_id == null) {
      abort(403, 'Необходимо авторизоваться под компанией');
    };
    $staffer->company_id = $company_id;
    $staffer->position_id = $position_id;
    $staffer->department_id = $department_id;
    $staffer->filial_id = $filial_id;
    $staffer->author_id = $user_id;
    $staffer->save();
    if ($staffer) {
      return Redirect('/current_department/'.$filial_id.'/'.$department_id);
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
    // Получаем метод
    $method = 'update';
    // ГЛАВНЫЙ ЗАПРОС:
    $staffer = Staffer::with(['employees' => function($query) {
                          $query->whereDate_dismissal(null);
                        }])
    ->withoutGlobalScope(ModerationScope::class)
    ->findOrFail($id);
    // Подключение политики
    $this->authorize($method, $staffer);
    // Список меню для сайта
    $answer = operator_right('sites', $this->entity_dependence, $method);
    $user = $request->user();
    $users = User::withoutGlobalScope($answer['moderator'])
    ->moderatorFilter($answer)
    ->companiesFilter($answer)
    ->filials($answer) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
    ->authors($answer)
    ->systemItem($answer) // Фильтр по системным записям
    ->orderBy('second_name')
    ->get();
    $users_list = [];
    foreach ($users as $user) {
      $users_list[$user->id] = $user->second_name.' '.$user->first_name;
    };
    // dd($users_list);
    return view('staff.edit', compact('staffer', 'users_list'));    
  }

  public function update(EmployeeRequest $request, $id)
  {
    // Получаем метод
    $method = __FUNCTION__;
    // Получаем авторизованного пользователя
    $user = $request->user();
    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, true, $method);
    // ГЛАВНЫЙ ЗАПРОС:
   $staffer = Staffer::withoutGlobalScope($answer['moderator'])->findOrFail($id);
    // Подключение политики
    $this->authorize('update', $staffer);
    // Если не пустая дата увольнения пришла
    if (isset($request->date_dismissal)) {
      // Снимаем с должности в штате
      $staffer->user_id = null;
      $staffer->editor_id = $user->id;
      // Ищем в сотрудниках по id должности и где пустая дата увольнения
      $employee = Employee::where(['staffer_id' => $id, 'date_dismissal' => null])->first();
      // Заполняем дату
      $employee->date_employment = $request->date_employment;
      $employee->date_dismissal = $request->date_dismissal;
      $employee->dismissal_desc = $request->dismissal_desc;
      $employee->editor_id = $user->id;
      // Удаляем должность и права данного юзера
      $delete = RoleUser::where(['position_id' => $staffer->position_id, 'user_id' => $staffer->user_id])->delete();
    // Если даты увольнения нет
    } else {
      $user_id = $staffer->user_id;
      $employee = Employee::where(['staffer_id' => $id, 'user_id' => $user_id, 'date_dismissal' => null])->first();
      if ($employee) {
          $date_employment_db = $employee->date_employment;
        // Смотрим отличатеся ли пришедшая дата устройства
        if ($date_employment_db !== $request->date_employment) {
          $employee->date_employment = $request->date_employment;
          $employee->save();
          if ($employee) {
            return Redirect('/staff');
          } else {
            abort(403, 'Ошибка при записи даты приема на должность!');
          };
        };
      } else {
        // Назначаем пользователя
        $staffer->user_id = $request->user_id;
        // Создаем новую запись в сотрудниках
        $employee = new Employee;
        $employee->company_id = $user->company_id;
        $employee->staffer_id = $id;
        $employee->user_id = $request->user_id;
        $employee->date_employment = $request->date_employment;
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
      };
    }; 
    $employee->save();
    if ($employee) {
      $staffer->save();
      if ($staffer) {
        $staffer->save();
        return Redirect('/staff');
      } else {
        abort(403, 'Ошибка при обновлении штата!');
      };
    } else {
      abort(403, 'Ошибка при обновлении сотрудника!');
    }; 
  }

  public function destroy($id)
  {
    // ГЛАВНЫЙ ЗАПРОС:
    $staffer = Staffer::with('department')->withoutGlobalScope(ModerationScope::class)->findOrFail($id);
    // Подключение политики
    $this->authorize('delete', $staffer);
    // Удаляем должность из отдела с обновлением
    // Находим филиал и отдел
    $user = $request->user();
    if (isset($staffer->department->filial_id)) {
      $filial_id = $staffer->department->filial_id;
      $department_id = $staffer->department_id;
    } else {
      $filial_id = $staffer->department_id;
      $department_id = 0;
    };
    $staffer->editor_id = $user->id;
    $staffer->save();
    $staffer = Staffer::destroy($id);
    if ($staffer) {
      return Redirect('/current_department/'.$filial_id.'/'.$department_id);
    } else {
      abort(403, 'Ошибка при удалении штата');
    };  
  }
}
