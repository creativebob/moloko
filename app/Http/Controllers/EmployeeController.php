<?php

namespace App\Http\Controllers;

use App\Employee;
use App\Position;
use App\Staffer;
use App\Department;
use App\User;
use App\Page;
use App\Company;

// Валидация
use Illuminate\Http\Request;
use App\Http\Requests\EmployeeRequest;

// Общие классы
use Illuminate\Support\Facades\Cookie;

class EmployeeController extends Controller
{

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
        $employees = Employee::with('staffer', 'staffer.position', 'staffer.filial', 'staffer.department', 'user')
        ->moderatorLimit($answer)
        ->companiesLimit($answer)

        // Так как сущность не филиала зависимая, но по факту
        // все таки зависимая через staff, то делаем нестандартную фильтрацию (прямо в запросе)
        ->when($answer['dependence'] == true, function ($query) use ($user) {
            return $query->whereHas('staffer', function($q) use ($user){
                $q->where('filial_id', $user->filial_id);
            });
        })

        // ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->booklistFilter($request)
        ->filter($request, 'position_id', 'staffer')
        ->filter($request, 'department_id', 'staffer')
        ->dateIntervalFilter($request, 'date_employment')
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->paginate(30);

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

        // Инфо о странице
        $page_info = pageInfo($this->entity_alias);

        return view('employees.index', compact('employees', 'page_info', 'filter'));
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
        $answer = operator_right($this->entity_alias,  $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $employee = Employee::with('user')->moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $employee);

        // Список пользователей
        $users_list = $employee->user->pluck('second_name', 'id');

        // Инфо о странице
        $page_info = pageInfo($this->entity_alias);

        return view('employees.edit', compact('employee', 'users_list', 'page_info'));
    }


    public function update(Request $request, $id)
    {

        // Получаем авторизованного пользователя
        $user = $request->user();

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias,  $this->entity_dependence, getmethod(__FUNCTION__));

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
          return redirect('/admin/employees');
        } else {
          abort(403, 'Ошибка редактирования сотрудника');
        };
    }

    public function destroy($id)
    {
        //
    }

    // Сортировка
    public function ajax_sort(Request $request)
    {

      $i = 1;

      foreach ($request->employees as $item) {
            Employee::where('id', $item)->update(['sort' => $i]);
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

        $item = Employee::where('id', $request->id)->update(['system_item' => $system]);

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

        $item = Employee::where('id', $request->id)->update(['display' => $display]);

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

}
