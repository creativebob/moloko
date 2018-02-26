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
        ->orderBy('moderation', 'desc')
        ->paginate(30);

        // Смотрим сколько филиалов в компании
        $user = $request->user();
        $answer_company = operator_right('companies', false, 'view');

        $company = Company::with(['departments' => function($query) use ($answer_company) {
          $query->moderatorLimit($answer_company)->whereFilial_status(1);
        }])->findOrFail($user->company_id);

        $filials = count($company->departments);

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);
        return view('employees.index', compact('employees', 'page_info', 'filials'));
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

        // Список меню для сайта
        $answer = operator_right('sites', false, 'index');

        $user = $request->user();

        $users_list = $employee->user->pluck('second_name', 'id');
      
      return view('employees.edit', compact('employee', 'users_list'));    
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
        $employee->date_employment = $request->date_employment;
        $employee->date_dismissal = $request->date_dismissal;
        $employee->dismissal_desc = $request->dismissal_desc;
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
