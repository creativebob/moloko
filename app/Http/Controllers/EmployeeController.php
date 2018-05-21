<?php

namespace App\Http\Controllers;

use App\Vacancy;
use App\Employee;
use App\Position;
use App\Staffer;
use App\Department;
use App\User;
use App\Page;
use App\Company;

// Валидация
use App\Http\Requests\EmployeeRequest;

// Политика
use App\Policies\EmployeePolicy;
use App\Policies\StafferPolicy;
use App\Policies\PositionPolicy;
use App\Policies\DepartmentPolicy;

// Подключаем фасады
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
  // Сущность над которой производит операции контроллер
  protected $entity_name = 'employees';
  protected $entity_dependence = false;

    public function index(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Employee::class);
        $this->authorize(getmethod(__FUNCTION__), Position::class);
        $this->authorize(getmethod(__FUNCTION__), Staffer::class);
        
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));
        // -------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -------------------------------------------------------------------------------------------
        $employees = Employee::with('staffer', 'staffer.position', 'staffer.filial', 'staffer.department', 'user')
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->filials($answer) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям

        ->booklistFilter($request)
        ->filter($request, 'position_id', 'staffer')
        ->filter($request, 'department_id', 'staffer')
        ->dateIntervalFilter($request, 'date_employment')
        ->orderBy('moderation', 'desc')
        ->paginate(30);

        // ---------------------------------------------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ----------------------------------------------------------------------------------------------------------------
        // ---------------------------------------------------------------------------------------------------------------------------------------------

        $filter_query = Employee::with('staffer.position', 'staffer.department')
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->filials($answer) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->get();

        // dd($filter_query);
        $filter['status'] = null;

        $filter = addFilter($filter, $filter_query, $request, 'Выберите должность:', 'position', 'position_id', 'staffer');
        $filter = addFilter($filter, $filter_query, $request, 'Выберите отдел:', 'department', 'department_id', 'staffer');


            // Добавляем данные по спискам (Требуется на каждом контроллере)
        $filter = addBooklist($filter, $filter_query, $request, $this->entity_name);
        // dd($filter);

        // ---------------------------------------------------------------------------------------------------------------------------------------------

        // Смотрим сколько филиалов в компании
        $user = $request->user();
        $answer_company = operator_right('companies', false, 'view');

        $company = Company::with(['departments' => function($query) use ($answer_company) {
          $query->moderatorLimit($answer_company)->whereFilial_status(1);
        }])->findOrFail($user->company_id);

        $filials = count($company->departments);

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        return view('employees.index', compact('employees', 'page_info', 'filials', 'filter'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        //
    }

    public function edit(Request $request, $id)
    {

        // Получаем авторизованного пользователя
        $user = $request->user();

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name,  $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $employee = Employee::with('user')->moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $employee);

        // Список пользователей
        $users_list = $employee->user->pluck('second_name', 'id');

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);
      
        return view('employees.edit', compact('employee', 'users_list', 'page_info'));    
    }


    public function update(Request $request, $id)
    {

        // Получаем авторизованного пользователя
        $user = $request->user();

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name,  $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $employee = Employee::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $employee);

        // Перезаписываем данные
        $employee->employment_date = $request->employment_date;
        $employee->dismissal_date = $request->dismissal_date;
        $employee->dismissal_description = $request->dismissal_description;
        $employee->editor_id = $user->id;
        $employee->save();
        
        // Если записалось
        if ($employee) {
          return Redirect('/employees');
        } else {
          abort(403, 'Ошибка редактирования сотрудника');
        };
    }

    public function destroy($id)
    {
        //
    }
}
