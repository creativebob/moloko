<?php

namespace App\Http\Controllers;

// Модели
use App\User;
use App\Department;
use App\Staffer;
use App\Employee;
use App\Role;
use App\Lead;

use App\Client;
use App\Loyalty;

use App\Dealer;
use App\Company;
use App\Page;
use App\Sector;
use App\Booklist;
use App\List_item;
use App\Schedule;
use App\Worktime;
use App\Location;
use App\ScheduleEntity;
use App\Country;
use App\ServicesType;
use App\Phone;

// Модели которые отвечают за работу с правами + политики
use App\Policies\CompanyPolicy;
use App\Policies\DealerPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

// Запросы и их валидация
use Illuminate\Http\Request;

use App\Http\Requests\CompanyRequest;
use App\Http\Requests\ClientRequest;

use App\Http\Requests\SupplierRequest;

// Общие классы
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

// Подрубаем трейт записи и обновления компании
use App\Http\Controllers\Traits\CompanyControllerTrait;
use App\Http\Controllers\Traits\UserControllerTrait;

class ClientController extends Controller
{

    // Сущность над которой производит операции контроллер
    protected $entity_name = 'clients';
    protected $entity_dependence = false;

    // Подключаем трейт записи и обновления компании
    use CompanyControllerTrait;
    use UserControllerTrait;

    public function index(Request $request)
    {

        $filter_url = autoFilter($request, $this->entity_name);
        if(($filter_url != null)&&($request->filter != 'active')){return Redirect($filter_url);};

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Client::class);

        // Получаем авторизованного пользователя
        $user = $request->user();

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // -------------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -------------------------------------------------------------------------------------------------------------

        $clients = Client::with('author', 'client.main_phones', 'loyalty')
        // ->withCount(['orders' => function($q) {
        //     $q->whereNull('draft');
        // }])
        ->moderatorLimit($answer)
        ->authors($answer)
        ->systemItem($answer)
        // ->filter($request, 'city_id', 'location')
        // ->filter($request, 'sector_id')
        ->booklistFilter($request)
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->paginate(30);

        // -----------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ------------------------------------------------------------------------------
        // -----------------------------------------------------------------------------------------------------------

        $filter = setFilter($this->entity_name, $request, [
            // 'city',                 // Город
            // 'sector',               // Направление деятельности
            'booklist'              // Списки пользователя
        ]);

        // Окончание фильтра -----------------------------------------------------------------------------------------



        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        return view('clients.index', compact('clients', 'page_info', 'filter', 'user'));
    }

    public function ajax_create(Request $request)
    {

        //Подключение политики
        // $this->authorize(getmethod(__FUNCTION__), Client::class);
        // $this->authorize(getmethod(__FUNCTION__), Company::class);
        // $this->authorize(getmethod(__FUNCTION__), User::class);
        // 
        $new_user = new User;
        $new_company = new Company;

        $new_company->name = $request->company_name;
        $new_company->email = $request->email;
        
        // ГЛАВНЫЙ ЗАПРОС:

        $lead = Lead::findOrFail($request->lead_id);

        // Чистка номера
        $main_phone = cleanPhone($request->main_phone);

        if(($request->company_name != null)||($lead->private_status == 1)){

            // ======================= РАБОТАЕМ С КОМПАНИЯМИ ============================
            $lead->private_status = 1;

            // if($request->inn != null){

            //     $find_company = Company::where('inn', $request->inn)->first();
            //     if($find_company){$new_company = $find_company;};

            // }

            // $find_company = Company::whereHas('phones', function($q) use ($main_phone){
            //     $q->where('phone', $main_phone);
            // })->first();

            // if($find_company){$new_company = $find_company;};


        } else {

            $lead->private_status = 0;

            // =============== РАБОТАЕМ С ФИЗИЧЕСКИМИ ЛИЦАМИ ============================

            // $lead->private_status = null;

            $search_user = User::whereHas('phones', function($q) use ($main_phone){
                $q->where('phone', $main_phone);
            })->first();

            // Если не найден, то создаем
            if(!isset($search_user)){

                $crop_name = explode(' ', $request->name);
                if(isset($crop_name[1])){$new_user->first_name = $crop_name[1];};
                if(isset($crop_name[0])){$new_user->second_name = $crop_name[0];};
                if(isset($crop_name[2])){$new_user->patronymic = $crop_name[2];};

                $new_user->email = $request->email;   
                // $new_user->email = $request->email ?? 'creativebob@maio.ru';  
            } else {
                $new_user = $search_user;
            };

        }

        // Получаем данные для авторизованного пользователя
        $user = $request->user();
        $user_id = $user->id;

        return view('includes.modals.modal-add-client', compact('new_user', 'user_id', 'lead', 'new_company'));
    }

    public function ajax_store(Request $request)
    {

        // Подключение политики
        // $this->authorize(getmethod(__FUNCTION__), Client::class);
        // $this->authorize(getmethod(__FUNCTION__), Company::class);

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Скрываем бога
        $user_id = hideGod($user);

        if($request->private_status == 1){

            $new_company = new Company;
            $new_company = $this->createCompany($request);

            if($request->lead_type_id == 2){

                $client = new Client;
                $client->client_id = $new_company->id;
                $client->client_type = 'App\Company';
                $client->company_id = $request->user()->company->id;

                // Запись информации по клиенту:
                // ...

                $client->save();

                $dealer = new Dealer;
                $dealer->client_id = $client->id;
                $dealer->company_id = $request->user()->company->id;

                // Запись информации по дилеру:
                // ...

                $dealer->save();

            } else {

                $client = new Client;
                $client->client_id = $new_company->id;
                $client->client_type = 'App\Company';
                $client->company_id = $request->user()->company->id;

                // Запись информации по клиенту:
                // ...

                $client->save();

            }

            $department = new Department;
            $department->name = 'Филиал';
            $department->company_id = $new_company->id;
            $department->location_id = $request->location_id;
            $department->filial_status = 1;
            $department->save();

            // Создаем пользователя
            // $request->access_block = 1;
            $new_user = $this->createUser($request);

            $new_user->company_id = $new_company->id;
            $new_user->save();

            $staffer = new Staffer;
            $staffer->user_id = $new_user->id;
            $staffer->position_id = 1; // Директор
            $staffer->department_id = $department->id;
            $staffer->filial_id = $department->id;
            $staffer->company_id = $new_company->id;
            $staffer->save();

            $employee = new Employee;
            $employee->company_id = $new_company->id;
            $employee->staffer_id = $staffer->id;
            $employee->user_id = $new_user->id;
            $employee->employment_date = Carbon::today()->format('Y-m-d');
            $employee->save();


        } else {

            // Чистка номера
            $main_phone = cleanPhone($request->main_phone);


            $user = User::has('client')
            ->whereHas('phones', function($q) use ($main_phone){
                    $q->where('phone', $main_phone);
            })->first();

            // Если не найден, то создаем
            if(!isset($client)){
                $new_user = $this->createUser($request);

                $client = new Client;
                $client->client_id = $new_user->id;
                $client->client_type = 'App\User';
                $client->company_id = $request->user()->company->id;
                $client->save();  
            }

        }


        // После создания клиента необходимо связать его с лидом
        $lead = Lead::findOrFail($request->lead_id);
        $lead->client_id = $client->id;
        $lead->save();

        return 'Ок';
    }


    public function store(ClientRequest $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Client::class);
        $this->authorize(getmethod(__FUNCTION__), Company::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Скрываем бога
        $user_id = hideGod($user);

        $client = new Client;

        // Создание нового клиента =========================================================

        // Компания 
        $new_company = new Company;

        // Отдаем работу по созданию новой компании трейту
        $company = $this->createCompany($request, $new_company);

        $new_user = new User;

        // Отдаем работу по созданию нового юзера трейту
        $user = $this->createUser($request, $new_user);

        $client->company_id = $request->user()->company->id;
        $client->client_id = $company->id;

        // Запись информации по клиенту:
        // ...


        $manufacturer->save();

        return redirect('/admin/manufacturers');
    }


    public function show($id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $dealer = Dealer::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize('view', $dealer);
        return view('dealers.show', compact('dealer'));
    }


    public function edit(Client $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $client = Client::moderatorLimit($answer)
        ->authors($answer)
        ->systemItem($answer)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $client);

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);


        // ПОЛУЧАЕМ КОМПАНИЮ ------------------------------------------------------------------------------------------------
        if($client->client_type == 'App\Company'){

            $company_id = $client->client->id;

            // Получаем из сессии необходимые данные (Функция находиться в Helpers)
            $answer_company = operator_right('companies', false, getmethod(__FUNCTION__));

            $company = Company::with('location.city', 'schedules.worktimes', 'sector', 'services_types')
            // ->moderatorLimit($answer_company)
            // ->authors($answer_company)
            // ->systemItem($answer_company)
            ->findOrFail($company_id);

            $this->authorize(getmethod(__FUNCTION__), $company);

            return view('clients.edit_client_company', compact('client', 'page_info'));
        }

        // ПОЛУЧАЕМ ФИЗ ЛИЦО ---------------------------------------------------------------------------------
        if($client->client_type == 'App\User'){


            $user_id = $client->client->id;

            // Получаем из сессии необходимые данные (Функция находиться в Helpers)
            $answer_user = operator_right('users', true, getmethod(__FUNCTION__));

            $user = User::with(
            'location.city',
            'photo',
            'main_phones',
            'extra_phones'
            )->moderatorLimit($answer_user)
            ->findOrFail($user_id);

            $this->authorize(getmethod(__FUNCTION__), $user);

            return view('clients.edit_client_user', compact('client', 'user', 'page_info'));
        }


    }


    public function update(SupplierRequest $request, $id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $client = Client::moderatorLimit($answer)
        ->authors($answer)
        ->systemItem($answer)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $client);


        // ПОЛУЧАЕМ КОМПАНИЮ ------------------------------------------------------------------------------------------------
        if($client->client_type == 'App\Company'){

            $company_id = $client->client->id;

            // Получаем из сессии необходимые данные (Функция находиться в Helpers)
            $answer_company = operator_right('companies', false, getmethod(__FUNCTION__));

            // ГЛАВНЫЙ ЗАПРОС:
            $company = Company::with('location', 'schedules.worktimes')->moderatorLimit($answer_company)->findOrFail($company_id);

            // Подключение политики
            $this->authorize(getmethod(__FUNCTION__), $company);

            // Отдаем работу по редактировнию компании трейту
            $this->updateCompany($request, $client->client);

        }

        if($client->client_type == 'App\User'){

            $user_id = $client->client->id;

            // Получаем из сессии необходимые данные (Функция находиться в Helpers)
            $answer_user = operator_right('users', false, getmethod(__FUNCTION__));

            // ГЛАВНЫЙ ЗАПРОС:
            $user = User::moderatorLimit($answer_user)->findOrFail($user_id);

            // Подключение политики
            $this->authorize(getmethod(__FUNCTION__), $user);

            // Отдаем работу по редактировнию юзера трейту
            $this->updateUser($request, $client->client);
        
        }

        // Обновление информации по клиенту:
        $client->description = $request->description;
        $client->loyalty_id = $request->loyalty_id;

        $client->save();

        return redirect('/admin/clients');
    }


    public function destroy(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $client = Client::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $client);

        if ($client) {

            $user = $request->user();

            // Скрываем бога
            $user_id = hideGod($user);
            $client->editor_id = $user_id;
            $client->save();

            $client = Client::destroy($id);

            // Удаляем компанию с обновлением
            if($client) {
                return redirect('/admin/clients');

            } else {
                abort(403, 'Ошибка при удалении клиента');
            }

        } else {
            abort(403, 'Клиент не найден');
        }
    }

    public function checkcompany(Request $request)
    {
        $company = Company::where('inn', $request->inn)->first();

        if(!isset($company)) {
            return 0;
        } else {
            return $company->name;};
    }

}
