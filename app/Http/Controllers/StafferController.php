<?php

namespace App\Http\Controllers;

use App\Staffer;
use App\Employee;
use App\Page;
use App\User;
use App\Company;
use App\Department;
use App\RoleUser;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StafferController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $user = Auth::user();
      if (isset($user->company_id)) {
        // Если у пользователя есть компания
        // $companies = Company::orderBy('company_name')->get()->pluck('company_name', 'id');
        $staff = Staffer::with('filial', 'department', 'user', 'position')->whereCompany_id($user->company_id)->paginate(30);
        $company = Company::with(['departments' => function($query) {
                      $query->whereFilial_status(1);
                    }])->findOrFail($user->company_id);
      } else {
        if ($user->god == 1) {
          // Если нет, то бог без компании
          // $companies = Company::orderBy('company_name')->get()->pluck('company_name', 'id');
          $staff = Staffer::with('filial', 'department', 'user', 'position')->paginate(30);
          $company = Company::with(['departments' => function($query) {
                      $query->whereFilial_status(1);
                    }])->findOrFail($user->company_id);
        };
      };
      $page_info = Page::wherePage_alias('/staff')->first();
      $filials = count($company->departments);
      // dd($filials);
      return view('staff.index', compact('staff', 'page_info', 'filials'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $user = Auth::user();

      // Пишем вакансию в бд
      $position_id = $request->position_id;
      $department_id = $request->parent_id;
      $filial_id = $request->filial_id;

      $staffer = new Staffer;

      $staffer->company_id = $user->company_id;
      $staffer->position_id = $position_id;
      $staffer->department_id = $department_id;
      $staffer->filial_id = $filial_id;
      $staffer->author_id = $user->id;

      $staffer->save();

      return Redirect('current_department/'.$filial_id.'/'.$department_id.'/'.$position_id);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
      $user = Auth::user();

      $staffer = Staffer::with(['employees' => function($q) {
                            $q->whereDate_dismissal(null);
                          }])->findOrFail($id);
      $users = User::whereCompany_id($user->company_id)->orderBy('second_name')->get()->pluck('second_name', 'id');

      // dd($staffer->user_id);
      
      return view('staff.edit', compact('staffer', 'users'));    
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
      $user = Auth::user();
      // Находим должность в штате
      $staffer = Staffer::findOrFail($id);
      
      // Если не пустая дата увольнения пришла
      if (isset($request->date_dismissal)) {
        // Снимаем с должности в штате
        $staffer->user_id = null;
        // Ищем в сотрудниках по id должности и где пустая дата увольнения
        $employee = Employee::whereStaffer_id($id)->whereDate_dismissal(null)->first();
        // Заполняем дату
        $employee->date_dismissal = $request->date_dismissal;
        $employee->dismissal_desc = $request->dismissal_desc;
        $employee->editor_id = $user->id;
        $staffer->editor_id = $user->id;
        // Удаляем должность и права данного юзера
        $delete = RoleUser::wherePosition_id($staffer->position_id)->whereUser_id($staffer->user_id)->delete();
      // Если даты увольнения нет
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
       
      }
     
      
      $employee->save();
      $staffer->save();

      return redirect('/staff');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      // Удаляем должность из отдела с обновлением
      // Находим филиал и отдел
      $staffer = Staffer::whereId($id)->first();
      
      $department_id = $staffer->department_id;
      $departments = Department::whereId($department_id)->first();

      if (isset($departments->filial_id)) {
        $filial_id = $departments->filial_id;
      } else {
        $filial_id = $department_id;
        $department_id = 0;
      };
      
      $staff = Staffer::destroy($id);
      // $city = true;
      if ($staff) {
        return Redirect('current_department/'.$filial_id.'/'.$department_id.'/0');
      } else {
        $data = [
          'status' => 0,
          'msg' => 'Произошла ошибка'
        ];
        echo 'произошла ошибка';
      };   
    }
}
