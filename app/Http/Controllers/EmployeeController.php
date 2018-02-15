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
        // Получаем метод
        $method = __FUNCTION__;
        // Подключение политики
        $this->authorize($method, Employee::class);
        $this->authorize($method, Position::class);
        $this->authorize($method, Staffer::class);
        // $this->authorize($method, Department::class);
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, $method);
        // -------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -------------------------------------------------------------------------------------------
        $employees = Employee::with('staffer', 'staffer.position', 'staffer.filial', 'staffer.department', 'user')
        ->withoutGlobalScope($answer['moderator'])
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->filials($answer) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->orderBy('moderated', 'desc')
        ->paginate(30);
        // Смотрим сколько филиалов в компании
        $user = $request->user();
        $company = Company::with(['departments' => function($query) {
          $query->whereFilial_status(1);
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
        // Получаем метод
        $method = 'update';
        // Получаем авторизованного пользователя
        $user = $request->user();
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, true, $method);
        // ГЛАВНЫЙ ЗАПРОС:
        $employee = Employee::with('user')->withoutGlobalScope($answer['moderator'])->findOrFail($id);
        // Подключение политики
        $this->authorize($method, $employee);
        // Список меню для сайта
        $answer = operator_right('sites', $this->entity_dependence, $method);
        $user = $request->user();
        $users_list = $employee->user->pluck('second_name', 'id');
      
      return view('employees.edit', compact('employee', 'users_list'));    
    }

    public function update(Request $request, $id)
    {
        // Получаем метод
        $method = __FUNCTION__;
        // Получаем авторизованного пользователя
        $user = $request->user();
        // ГЛАВНЫЙ ЗАПРОС:
        $employee = Employee::withoutGlobalScope(ModerationScope::class)->findOrFail($id);
        // Подключение политики
        $this->authorize('update', $employee);
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
