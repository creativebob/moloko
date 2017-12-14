<?php

namespace App\Http\Controllers;

// Модели для текущей работы
use App\User;
use App\Company;

// Модели которые отвечают за работу с правами + политики
use App\Access;
use App\Access_group;
use App\Policies\CompanyPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

// Запросы и их валидация
use Illuminate\Http\Request;
use App\Http\Requests\UpdateCompany;

// Прочие необходимые классы
use Illuminate\Support\Facades\Log;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('index', Company::class);

        $companies = Company::paginate(30);
        return view('companies.index', compact('companies'), compact('access'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create', Company::class);

        $company = new Company;
        return view('companies.create', compact('company'));   

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('create', Company::class);

        $company = new Company;
        $company->company_name = $request->company_name;
        $company->company_phone = cleanPhone($request->company_phone);

        if(($request->company_extra_phone != NULL)&&($request->company_extra_phone != "")){
            $company->company_extra_phone = cleanPhone($request->company_extra_phone);
        } else {$company->company_extra_phone = NULL;};

        $company->city_id = $request->city_id;
        $company->company_address = $request->company_address;

        $company->company_inn = $request->inn;
        $company->kpp = $request->kpp;
        $company->account_settlement = $request->account_settlement;
        $company->account_correspondent = $request->account_correspondent;

        $company->save();

        return redirect('companies');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $company = Company::findOrFail($id);
        $this->authorize('view', $company);

        return view('companies.show', compact('company'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $company = Company::findOrFail($id);
        $this->authorize('update', $company);

        return view('companies.show', compact('company'));
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
        $company = Company::findOrFail($id);
        $this->authorize('update', $company);

        $company->company_name = $request->company_name;
        $company->company_phone = cleanPhone($request->company_phone);

        if(($request->company_extra_phone != NULL)&&($request->company_extra_phone != "")){
            $company->company_extra_phone = cleanPhone($request->company_extra_phone);
        } else {$company->company_extra_phone = NULL;};

        $company->city_id = $request->city_id;
        $company->company_address = $request->company_address;

        $company->company_inn = $request->inn;
        $company->kpp = $request->kpp;
        $company->account_settlement = $request->account_settlement;
        $company->account_correspondent = $request->account_correspondent;
        $company->bank = $request->bank;
        $company->user_id = $request->user_id;

        $company->save();
        return redirect('companies');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $company = User::findOrFail($id);
        $this->authorize('delete', $company);   

        $company = Company::destroy($id);
        if ($company){
        $data = [
          'status'=> 1,
          'msg' => 'Успешно удалено'
        ];
        } else {
          $data = [
          'status' => 0,
          'msg' => 'Произошла ошибка'
        ];
        };
        echo json_encode($data, JSON_UNESCAPED_UNICODE);

        Log::info('Удалили запись из таблица Компании. ID: ' . $id);
    }
}
