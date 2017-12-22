<?php

namespace App\Http\Controllers;

use App\Employee;
use App\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
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
      $employees = Employee::whereCompany_id($user->company_id)->paginate(30);
    } else {
      if ($user->god == 1) {
        // Если нет, то бог без компании
        // $companies = Company::orderBy('company_name')->get()->pluck('company_name', 'id');
        $employees = Employee::paginate(30);
      };
    };
    $page_info = Page::wherePage_alias('/employees')->first();
    $menu = Page::whereSite_id('1')->get();
    return view('employees.index', compact('employees', 'page_info', 'menu'));
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

      $employee = new Employee;

      $employee->company_id = $user->id;
      $employee->position_id = $position_id;
      $employee->department_id = $department_id;
      $employee->author_id = $user->id;

      $employee->save();

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
        //
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
        //
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
      $employees = Employee::whereId($id)->first();
      
      $department_id = $employees->department_id;
      $departments = Department::whereId($department_id)->first();

      if (isset($departments->filial_id)) {
        $filial_id = $departments->filial_id;
      } else {
        $filial_id = $department_id;
        $department_id = 0;
      };
      
      $employee = Employee::destroy($id);
      // $city = true;
      if ($employee) {
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
