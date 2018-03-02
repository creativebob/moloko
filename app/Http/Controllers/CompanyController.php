<?php

namespace App\Http\Controllers;

// Модели для текущей работы
use App\User;
use App\Company;
use App\Page;
use App\Sector;
use App\Folder;

// Модели которые отвечают за работу с правами + политики
use App\Policies\CompanyPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

// Запросы и их валидация
use Illuminate\Http\Request;
use App\Http\Requests\CompanyRequest;



// Прочие необходимые классы
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CompanyController extends Controller
{

    // Сущность над которой производит операции контроллер
    protected $entity_name = 'companies';
    protected $entity_dependence = false;

    public function index(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Company::class);

        // Получаем авторизованного пользователя
        $user = $request->user();

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));
        // dd($answer);

        // ---------------------------------------------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // ---------------------------------------------------------------------------------------------------------------------------------------------

        $companies = Company::with('author', 'director', 'city')
        ->moderatorLimit($answer)
        ->moderatorLimit($answer)
        ->companyFilter($request)
        ->orderBy('moderation', 'desc')
        ->paginate(30);

        // ---------------------------------------------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ----------------------------------------------------------------------------------------------------------------
        // ---------------------------------------------------------------------------------------------------------------------------------------------

        $filter_query = Company::with('city')->moderatorLimit($answer)->get();
        $filter = getFilterCompany($filter_query);

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        return view('companies.index', compact('companies', 'page_info', 'filter', 'user'));
    }


    public function create(Request $request)
    {

        //Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Company::class);

        // Подключение политики
        $company = new Company;

        // Получаем список секторов
        $sectors = Sector::get()->keyBy('id')->toArray();
        $sectors_cat = [];
        foreach ($sectors as $id => &$node) {   
          //Если нет вложений
          if (!$node['sector_parent_id']){
            $sectors_cat[$id] = &$node;
          } else { 
          //Если есть потомки то перебераем массив
            $sectors[$node['sector_parent_id']]['children'][$id] = &$node;
          };
        };
        // dd($sectors_cat);
        $sectors_list = [];
        foreach ($sectors_cat as $id => &$node) {
            $sectors_list[$id] = &$node;
            if (isset($node['children'])) {
                foreach ($node['children'] as $id => &$node) {
                    $sectors_list[$id] = &$node;
                }
            };
        };
        // dd($sectors_list);

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        return view('companies.create', compact('company', 'sectors_list', 'page_info'));
    }


    public function store(CompanyRequest $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Company::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // Получаем авторизованного пользователя
        $user = $request->user();

        $company = new Company;
        $company->company_name = $request->company_name;
        $company->phone = cleanPhone($request->phone);
        $company->email = $request->email;

        if(($request->extra_phone != NULL)&&($request->extra_phone != "")){
            $company->extra_phone = cleanPhone($request->extra_phone);
        } else {$company->extra_phone = NULL;};

        $company->city_id = $request->city_id;
        $company->address = $request->address;

        $company->inn = $request->inn;
        $company->kpp = $request->kpp;
        $company->account_settlement = $request->account_settlement;
        $company->account_correspondent = $request->account_correspondent;

        $company->sector_id = $request->sector_id;

        // $company->director_user_id = $user->company_id;
        $company->author_id = $user->id;

        $company->save();

        $folder = new Folder;
        $folder->folder_name = $company->company_name;



        //Создаем папку в файловой системе
        $link_for_folder = 'public/companies/' . $company->id . '/users';

        Storage::makeDirectory($link_for_folder);

        $folder->folder_url = $link_for_folder;
        $folder->folder_alias = 'users';
        $folder->folder_parent_id = 2;
        $folder->save();
        
        return redirect('companies');
    }


    public function show($id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $company = Company::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize('view', $company);
        return view('companies.show', compact('company'));
    }


    public function edit($id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        $company = Company::with('city')->moderatorLimit($answer)->findOrFail($id);
        $this->authorize(getmethod(__FUNCTION__), $company);

        // Получаем список секторов
        $sectors = Sector::get()->keyBy('id')->toArray();
        $sectors_cat = [];
        foreach ($sectors as $id => &$node) {   
          //Если нет вложений
          if (!$node['sector_parent_id']){
            $sectors_cat[$id] = &$node;
          } else { 
          //Если есть потомки то перебераем массив
            $sectors[$node['sector_parent_id']]['children'][$id] = &$node;
          };
        };
        // dd($sectors_cat);
        $sectors_list = [];
        foreach ($sectors_cat as $id => &$node) {
            $sectors_list[$id] = &$node;
            if (isset($node['children'])) {
                foreach ($node['children'] as $id => &$node) {
                    $sectors_list[$id] = &$node;
                }
            };
        };

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        return view('companies.edit', compact('company', 'sectors_list', 'page_info'));
    }


    public function update(CompanyRequest $request, $id)
    {

        // Получаем авторизованного пользователя
        $user = $request->user();

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $company = Company::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $company);

        $company->company_name = $request->company_name;
        $company->phone = cleanPhone($request->phone);
        $company->email = $request->email;

        if(($request->extra_phone != NULL)&&($request->extra_phone != "")){
            $company->extra_phone = cleanPhone($request->extra_phone);
        } else {$company->extra_phone = NULL;};

        $company->city_id = $request->city_id;
        $company->address = $request->address;

        $company->inn = $request->inn;
        $company->kpp = $request->kpp;
        $company->account_settlement = $request->account_settlement;
        $company->account_correspondent = $request->account_correspondent;
        $company->bank = $request->bank;

        $company->sector_id = $request->sector_id;

        // $company->director_user_id = Auth::user()->company_id;

        $company->save();
        return redirect('companies');
    }


    public function destroy($id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $company = Company::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $company);

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
        $company = Company::where('inn', $request->inn)->first();

        if(!isset($company)){
            return 0;} else {
            return $company->company_name;};
    }

}
