<?php

namespace App\Http\Controllers;

// Модели для текущей работы
use App\User;
use App\Company;
use App\Page;

// Модели которые отвечают за работу с правами + политики
use App\Policies\CompanyPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

// Запросы и их валидация
use Illuminate\Http\Request;
use App\Http\Requests\CompanyRequest;

// Прочие необходимые классы
use Illuminate\Support\Facades\Log;

class CompanyController extends Controller
{

    // Сущность над которой производит операции контроллер
    protected $entity_name = 'companies';
    protected $entity_dependence = false;

    public function index(Request $request)
    {

        // Получаем метод
        $method = __FUNCTION__;

        // Подключение политики
        $this->authorize($method, Company::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, $method);
        // dd($answer);

        // ---------------------------------------------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // ---------------------------------------------------------------------------------------------------------------------------------------------

        $companies = Company::with('author', 'director')
        ->withoutGlobalScope($answer['moderator'])
        ->moderatorFilter($answer)
        // ->companiesFilter($answer['company_id'])
        // ->filials($answer['filials'], $answer['dependence']) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
        // ->authors($answer['all_authors'])
        // ->systemItem($answer['system_item'], $answer['user_status'], $answer['company_id']) // Фильтр по системным записям
        // ->orWhere('id', $request->user()->id) // Только для сущности USERS
        ->orderBy('moderated', 'desc')
        ->paginate(30);

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        return view('companies.index', compact('companies', 'page_info'));
    }


    public function create(Request $request)
    {
        // Получаем метод
        $method = __FUNCTION__;

        // Подключение политики
        $this->authorize(__FUNCTION__, Company::class);

        $company = new Company;
        return view('companies.create', compact('company'));   
    }


    public function store(CompanyRequest $request)
    {

        // Получаем метод
        $method = __FUNCTION__;

        // Подключение политики
        $this->authorize('create', Company::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, $method);

        // Получаем авторизованного пользователя
        $user = $request->user();

        $company = new Company;
        $company->company_name = $request->company_name;
        $company->company_phone = cleanPhone($request->company_phone);
        $company->company_email = $request->company_email;

        if(($request->company_extra_phone != NULL)&&($request->company_extra_phone != "")){
            $company->company_extra_phone = cleanPhone($request->company_extra_phone);
        } else {$company->company_extra_phone = NULL;};

        $company->city_id = $request->city_id;
        $company->company_address = $request->company_address;

        $company->company_inn = $request->company_inn;
        $company->kpp = $request->kpp;
        $company->account_settlement = $request->account_settlement;
        $company->account_correspondent = $request->account_correspondent;

        $company->director_user_id = $user->company_id;
        $company->author_id = $user->id;

        $company->save();
        
        return redirect('companies');
    }


    public function show($id)
    {

        // ГЛАВНЫЙ ЗАПРОС:
        $company = Company::withoutGlobalScope(ModerationScope::class)->findOrFail($id);

        // Подключение политики
        $this->authorize('view', $company);
        return view('companies.show', compact('company'));
    }


    public function edit($id)
    {
        $company = Company::with('city')->findOrFail($id);
        // $this->authorize('update', $company);

        return view('companies.show', compact('company'));
    }


    public function update(CompanyRequest $request, $id)
    {

        // Получаем метод
        $method = __FUNCTION__;

        // Получаем авторизованного пользователя
        $user = $request->user();

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, $method);

        // ГЛАВНЫЙ ЗАПРОС:
        $company = Company::withoutGlobalScope($answer['moderator'])->findOrFail($id);

        // Подключение политики
        $this->authorize('update', $company);

        $company->company_name = $request->company_name;
        $company->company_phone = cleanPhone($request->company_phone);
        $company->company_email = $request->company_email;

        if(($request->company_extra_phone != NULL)&&($request->company_extra_phone != "")){
            $company->company_extra_phone = cleanPhone($request->company_extra_phone);
        } else {$company->company_extra_phone = NULL;};

        $company->city_id = $request->city_id;
        $company->company_address = $request->company_address;

        $company->company_inn = $request->company_inn;
        $company->kpp = $request->kpp;
        $company->account_settlement = $request->account_settlement;
        $company->account_correspondent = $request->account_correspondent;
        $company->bank = $request->bank;
        $company->director_user_id = Auth::user()->company_id;

        $company->save();
        return redirect('companies');
    }


    public function destroy($id)
    {

        // ГЛАВНЫЙ ЗАПРОС:
        $company = Company::withoutGlobalScope(ModerationScope::class)->findOrFail($id);

        // Подключение политики
        $this->authorize('delete', $company);

        $company = Company::destroy($id);

        if($company) {
          return Redirect('/companies');
        } else {
          echo 'произошла ошибка';
        }; 

        Log::info('Удалили запись из таблицы Компании. ID: ' . $id);
    }


    public function checkcompany(Request $request)
    {
        $company = Company::where('company_inn', $request->company_inn)->first();

        if(!isset($company)){
            return 0;} else {
            return $company->company_name;};
    }

}
