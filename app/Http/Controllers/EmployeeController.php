<?php

namespace App\Http\Controllers;

use App\Vacancy;
use App\Employee;
use App\User;
use App\Page;
use App\Company;
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
        $employees = Employee::with('staffer', 'staffer.position', 'staffer.filial', 'staffer.department', 'user')->whereCompany_id($user->company_id)->paginate(30);
        // Смотрим сколько филиалов в компании
        $company = Company::with(['departments' => function($query) {
                      $query->whereFilial_status(1);
                    }])->findOrFail($user->company_id);
        $filials = count($company->departments);
      } else {
        if ($user->god == 1) {
        // Если нет, то бог без компании
        // $companies = Company::orderBy('company_name')->get()->pluck('company_name', 'id');
        $employees = Employee::with('staffer')->paginate(30);
        $filials = 2;
        };
      };
      $page_info = pageInfo('employees');
      return view('employees.index', compact('employees', 'page_info', 'filials'));
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
        //
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

      $employee = Employee::findOrFail($id);
      $users = User::whereCompany_id($user->company_id)->orderBy('second_name')->get()->pluck('second_name', 'id');

      // dd($staffer->user_id);
      
      return view('employees.edit', compact('employee', 'users'));    
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
        //
    }
}
