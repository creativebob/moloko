<?php

namespace App\Http\Controllers;

// Модели
use App\User;
use App\Company;
use App\Bank;
use App\BankAccount;
use App\Page;
use App\Sector;
use App\Folder;
use App\Booklist;
use App\List_item;
use App\Schedule;
use App\Worktime;
use App\Location;
use App\ScheduleEntity;
use App\Supplier;
use App\Manufacturer;
use App\Country;
use App\ProcessesType;
use App\Phone;

// Транслитерация
use Illuminate\Support\Str;

// Модели которые отвечают за работу с правами + политики
use App\Policies\CompanyPolicy;
use App\Policies\SupplierPolicy;
use App\Policies\ManufacturerPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

// Общие классы
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cookie;

// Запросы и их валидация
use Illuminate\Http\Request;
use App\Http\Requests\CompanyRequest;

// Прочие необходимые классы
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

// Подрубаем трейт записи и обновления компании
use App\Http\Controllers\Traits\CompanyControllerTrait;
use App\Http\Controllers\Traits\UserControllerTrait;
use App\Http\Controllers\Traits\DepartmentControllerTrait;

class CompanyController extends Controller
{

    // Сущность над которой производит операции контроллер
    protected $entity_name = 'companies';
    protected $entity_dependence = false;

    // Подключаем трейт записи и обновления компании
    use CompanyControllerTrait;
    use UserControllerTrait;
    use DepartmentControllerTrait;

    public function index(Request $request)
    {

        $filter_url = autoFilter($request, $this->entity_name);
        if(($filter_url != null)&&($request->filter != 'active')){return Redirect($filter_url);};

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Company::class);

        // Получаем авторизованного пользователя
        $user = $request->user();

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ------------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // ------------------------------------------------------------------------------------------------------------

        $companies = Company::with('author', 'location.city', 'sector', 'we_suppliers', 'we_manufacturers', 'we_clients', 'main_phones', 'legal_form', 'director')
        ->moderatorLimit($answer)
        ->authors($answer)
        ->systemItem($answer)
        ->filter($request, 'city_id', 'location')
        ->filter($request, 'sector_id')
        ->booklistFilter($request)
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->paginate(30);

        // -----------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ------------------------------------------------------------------------------
        // -----------------------------------------------------------------------------------------------------------

        $filter = setFilter($this->entity_name, $request, [
            'author',               // Автор записи
            'sector',               // Направление деятельности
            'booklist'              // Списки пользователя
        ]);

        // Окончание фильтра -----------------------------------------------------------------------------------------


        // dd($companies->get(3)->director->user->name_reverse);

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
        $user = new User;

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        return view('companies.create', compact('company', 'user', 'page_info'));
    }

    public function store(CompanyRequest $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Company::class);

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Скрываем бога
        $user_id = hideGod($user);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // dd($request);
        // Отдаем работу по созданию новой компании трейту
        $new_company = $this->createCompany($request);

        // Следом автоматически создаем первый филиал у компании
        $new_department = $this->createFirstDepartment($new_company);

        // Чистка номера
        $main_phone = cleanPhone($request->main_phone);

        $new_user = User::whereHas('main_phones', function($q) use ($main_phone){
            $q->where('phone', $main_phone);
        })->first();

        Log::info('Поискали номер телефона в базе...');

        // Если не найден, то создаем
        if(!isset($new_user)){

            $new_user = $this->createUserByPhone($request->user_phone, $request);

            $new_user->location_id = create_location($request, $request->country_id_default, $request->user_city_id, $request->user_address);
            $new_user->user_type = 1; // Делаем его внутренним пользователем системы
            $new_user->save();
            Log::info('Создали юзера');

            // Дописываем юзеру недостающие данные
            $new_user->company_id = $new_company->id;
            $new_user->filial_id = $new_department->id;
            $new_user->save();

        } else {

            Log::info('Найден пользователь с таким номером телефона. ID: ' . $new_user);

        }

        // Создаем штатную единицу директора и устраиваем на нее юзера
        $employee = $this->createDirector($new_company, $new_department, $new_user);

        return redirect('/admin/companies');
    }

    public function show($id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $company = Company::moderatorLimit($answer)
        ->authors($answer)
        ->systemItem($answer)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize('view', $company);

        return view('companies.show', compact('company'));
    }

    public function edit(Request $request, $id)
    {

        // Получаем данные для авторизованного пользователя
        $user_auth = $request->user();

        // Скрываем бога
        $user_auth_id = hideGod($user_auth);
        $company_id = $user_auth->company_id;


        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        $company = Company::with(
            'extra_phones',
            'bank_accounts.bank')
        ->moderatorLimit($answer)
        ->authors($answer)
        ->systemItem($answer)
        ->findOrFail($id);

        $this->authorize(getmethod(__FUNCTION__), $company);

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        return view('companies.edit', compact('company', 'page_info'));
    }

    public function update(CompanyRequest $request, $id)
    {

        // Получаем авторизованного пользователя
        $user = $request->user();

        // Скрываем бога
        $user_id = hideGod($user);

        // Компания пользователя
        $user_company = $user->company;

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $company = Company::with('location', 'schedules.worktimes')
        ->moderatorLimit($answer)
        ->authors($answer)
        ->systemItem($answer)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $company);

        // Отдаем работу по редактировнию компании трейту
        $this->updateCompany($request, $company);

        return redirect('/admin/companies');
    }


    public function destroy(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $company = Company::moderatorLimit($answer)
        ->authors($answer)
        ->systemItem($answer)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $company);

        if($company) {

            $user = $request->user();
            $user_id = hideGod($user);

            $company->editor_id = $user_id;
            $company->save();

            $company = Company::destroy($id);

            // Удаляем компанию с обновлением
            if($company) {

                return redirect('/admin/companies');

            } else {
                abort(403, 'Ошибка при удалении компании');
            }

        } else {
            abort(403, 'Компания не найдена');
        }
    }

    // ------------------------------------------- Ajax ---------------------------------------------

    public function checkcompany(Request $request)
    {
        $company = Company::where('inn', $request->inn)->first();

        if(!isset($company)) {
            return 0;
        } else {
            return $company->name;
        }
    }

}

