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
use App\Http\Controllers\Traits\LeadControllerTrait;
use App\Http\Controllers\Traits\DepartmentControllerTrait;

class ClientController extends Controller
{

    // Сущность над которой производит операции контроллер
    protected $entity_name = 'clients';
    protected $entity_dependence = false;

    // Подключаем трейт записи и обновления компании
    use CompanyControllerTrait;
    use UserControllerTrait;
    use LeadControllerTrait;
    use DepartmentControllerTrait;

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

        $clients = Client::with('author', 'clientable.main_phones', 'loyalty')
        // ->withCount(['orders' => function($q) {
        //     $q->whereNull('draft');
        // }])
        ->companiesLimit($answer)
        // ->filials($answer) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
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


        }

            // Обработка входящих данных ------------------------------------------

            // Можно будет использовать машиное обучение


            // $crop_name = explode(' ', $request->name);
            // Log::info('Пробуем разбить пришедшее имя на части');

            // if(isset($crop_name[0])){$first_name_gen = $crop_name[0];};
            // if(isset($crop_name[1])){$second_name_gen = $crop_name[1];};
            // if(isset($crop_name[2])){$patronymic_gen = $crop_name[2];};

            // Конец обработки ------------------------------------------------------


            $search_user = User::whereHas('phones', function($q) use ($main_phone){
                $q->where('phone', $main_phone);
            })->first();

            // Если не найден, то создаем
            if(!isset($search_user)){


                // ПОДСТАНОВКА в случае отсутствия

                $new_user->first_name = $first_name_gen ?? $request->name ?? 'Укажите фамилию';
                $new_user->second_name = $second_name_gen ?? null;
                $new_user->patronymic = $patronymic_gen ?? null;

                $new_user->email = $request->email;

            } else {

                $new_user = $search_user;
                if(($new_user->first_name == null)&&($new_user->first_name == null)){

                    // Если поля имя и отчество не заполнены
                    if(isset($first_name_gen)){
                        $new_user->first_name = $first_name_gen;
                    }

                    if(isset($second_name_gen)){
                        $new_user->second_name = $second_name_gen;
                    }

                }
            };



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

        Log::info('Попытка создания реквизитов клиента');

        if($request->private_status == 1){

            Log::info('Видим, что это компания');
            $new_company = $this->createCompany($request);

            if($request->lead_type_id == 2){

                Log::info('Видим, что это дилерский лид');
                $client = new Client;
                $client->clientable_id = $new_company->id;
                $client->clientable_type = 'App\Company';
                $client->company_id = $request->user()->company->id;

                // Запись информации по клиенту:
                // ...

                $client->save();
                Log::info('Сохраняем клиента компанию');

                $dealer = new Dealer;
                $dealer->client_id = $client->id;
                $dealer->company_id = $request->user()->company->id;
                $dealer->author_id = $user_id;

                // Запись информации по дилеру:
                // ...

                $dealer->save();

            } else {

                Log::info('Видим, что это обычный лид');
                $client = new Client;
                $client->clientable_id = $new_company->id;
                $client->clientable_type = 'App\Company';
                $client->company_id = $request->user()->company->id;
                $client->author_id = $user_id;

                // Запись информации по клиенту:
                // ...

                $client->save();
                Log::info('Сохраняем клиента компанию');
            }

            // Создаем первый филиал
            $new_department = $this->createFirstDepartment($new_company);

            Log::info('Сейчас будем писать юзера');
            $new_user = $this->createUser($request);

            // Добавляем после сохранения юзера еще инфы и снова сохраняем
            $new_user->company_id = $new_company->id;
            $new_user->save();
            Log::info('Сохраняем нового юзера');

            // Создаем штатную единицу директора и устраиваем на нее юзера
            $employee = $this->createDirector($new_company, $new_department, $new_user);

        } else {

            Log::info('Видим, что это физик');

            // Чистка номера
            $main_phone = cleanPhone($request->main_phone);
            Log::info('Вычистили номер телефона: ' . $main_phone);

            $user_for_client = User::whereHas('main_phones', function($q) use ($main_phone){
                $q->where('phone', $main_phone);
            })->first();

            // Если не найден, то создаем
            if(!isset($user_for_client)){
                Log::info('Пользователь с таким номером телефона не встречается - будем создавать юзера!');

                $user_for_client = $this->createUser($request);
                Log::info('Создали юзера');

            } else {

                Log::info('Найден пользователь с таким номером телефона. ID: ' . $user_for_client);
                $user_for_client = $this->updateUser($request, $user_for_client);

            }

            // Ищем есть ли клиент с таким пользователем
            $client = Client::where('clientable_id', $user_for_client->id)->where('clientable_type', 'App\User')->first();


            if(!isset($client)){

                Log::info('Будем создавать клиента');
                $client = new Client;
                $client->clientable_id = $user_for_client->id;
                $client->clientable_type = 'App\User';
                $client->company_id = $request->user()->company->id;
                $client->save();
                Log::info('Создали клиента');

            } else {

                Log::info('Есть такой клиент - берем его');

            };

        }

        // После создания клиента необходимо связать его с лидом
        $lead = Lead::findOrFail($request->lead_id);
        $lead->client_id = $client->id;

        // Выводим из черновика, так как создали юзера / клиента и связали с лидом
        $lead->draft = null;

        $this->updateLead($request, $lead);

        // Если для лида еще не указали имя, берем его из карточки реквизитов
        if($lead->name == null){

            $lead_first_name = $user_for_client->first_name ?? 'Имя';
            $lead_second_name = $user_for_client->second_name ?? 'Фамилия';
            $lead->name = $lead_first_name . ' ' . $lead_second_name;
        }

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
        $client->clientable_id = $company->id;

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
        if($client->clientable_type == 'App\Company'){

            $company_id = $client->clientable->id;

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
        if($client->clientable_type == 'App\User'){


            $user_id = $client->clientable->id;

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
        if($client->clientable_type == 'App\Company'){

            $company_id = $client->clientable->id;

            // Получаем из сессии необходимые данные (Функция находиться в Helpers)
            $answer_company = operator_right('companies', false, getmethod(__FUNCTION__));

            // ГЛАВНЫЙ ЗАПРОС:
            $company = Company::with('location', 'schedules.worktimes')->moderatorLimit($answer_company)->findOrFail($company_id);

            // Подключение политики
            $this->authorize(getmethod(__FUNCTION__), $company);

            // Отдаем работу по редактировнию компании трейту
            $this->updateCompany($request, $client->clientable);

        }

        if($client->clientable_type == 'App\User'){

            $user_id = $client->clientable->id;

            // Получаем из сессии необходимые данные (Функция находиться в Helpers)
            $answer_user = operator_right('users', false, getmethod(__FUNCTION__));

            // ГЛАВНЫЙ ЗАПРОС:
            $user = User::moderatorLimit($answer_user)->findOrFail($user_id);

            // Подключение политики
            $this->authorize(getmethod(__FUNCTION__), $user);

            // Отдаем работу по редактировнию юзера трейту
            $this->updateUser($request, $client->clientable);
        
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
