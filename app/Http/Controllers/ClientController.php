<?php

namespace App\Http\Controllers;

// Модели
use App\User;
use App\Lead;
use App\Client;
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
use App\Http\Requests\SupplierRequest;

// Общие классы
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ClientController extends Controller
{

    // Сущность над которой производит операции контроллер
    protected $entity_name = 'clients';
    protected $entity_dependence = false;

    public function index(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Client::class);

        // Получаем авторизованного пользователя
        $user = $request->user();

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // -------------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -------------------------------------------------------------------------------------------------------------

        $clients = Client::with('author', 'contragent.main_phones')
        ->withCount(['orders' => function($q) {
            $q->whereNull('draft');
        }])
        ->where('company_id', '!=', null)
        ->moderatorLimit($answer)
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

        // Подключение политики
        // $this->authorize(getmethod(__FUNCTION__), Client::class);

        // $client = new Client;

        $new_company = new Company;
        $new_company->name = $request->company_name;
        $new_company->email = $request->email;
        // ГЛАВНЫЙ ЗАПРОС:
        // 
        $lead = Lead::findOrFail($request->lead_id);
 
        $new_user = new User;

        $crop_name = explode(" ", $request->name);
        if(isset($crop_name[1])){$new_user->first_name = $crop_name[1];};
        if(isset($crop_name[0])){$new_user->second_name = $crop_name[0];};
        if(isset($crop_name[2])){$new_user->patronymic = $crop_name[2];};

        $new_user->email = $request->email;

        // $new_user->location = $request->name;
        // $new_user->second_name = $request->name;

        // Получаем данные для авторизованного пользователя
        $user = $request->user();
        $user_id = $user->id;

        return view('includes.modals.modal-add-client', compact('new_user', 'user_id', 'lead', 'new_company'));
    }

    public function store(CompanyRequest $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Dealer::class);
        // $this->authorize(getmethod(__FUNCTION__), Company::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Смотрим компанию пользователя
        $company_id = $user->company_id;
        $company = $user->company;

        // Скрываем бога
        $user_id = hideGod($user);

        $schedule = new Schedule;
        $schedule->company_id = $company_id;
        $schedule->name = 'График работы компании';
        $schedule->description = null;
        $schedule->author_id = $user_id;
        $schedule->save();
        $schedule_id = $schedule->id;

        // Функция getWorktimes ловит все поля расписания из запроса и готовит к записи в worktimes
        $mass_time = getWorktimes($request, $schedule_id);

        // Записываем в базу все расписание.
        DB::table('worktimes')->insert($mass_time);

        $client = new Company;
        $client->name = $request->name;
        $client->alias = $request->alias;

        $client->email = $request->email;

        // Добавляем локацию
        $client->location_id = create_location($request);

        $client->inn = $request->inn;
        $client->kpp = $request->kpp;
        $client->account_settlement = $request->account_settlement;
        $client->account_correspondent = $request->account_correspondent;

        $client->sector_id = $request->sector_id;
        $client->schedule_id = $schedule->id;

        // $company->director_user_id = $user->company_id;
        $client->author_id = $user->id;

        $client->save();

        // Если запись удачна - будем записывать связи
        if($client){

            // Записываем компанию как клиента
            $company->clients()->attach($client->id);

            // Телефон
            $phones = add_phones($request, $client);

            // Записываем связи: id-шники в таблицу Rooms
            if(isset($request->services_types_id)){
                
                $result = $client->services_types()->sync($request->services_types_id);               
            } else {
                $result = $client->services_types()->detach(); 
            };

        } else {
            abort(403, 'Ошибка записи компании');
        };


        // Создаем связь расписания с компанией
        $schedule_entity = new ScheduleEntity;
        $schedule_entity->schedule_id = $schedule->id;
        $schedule_entity->entity_id = $company->id;
        $schedule_entity->entity = 'companies';
        $schedule_entity->save();

        return redirect('/admin/dealers');
        // return redirect('admin/companies');
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


    public function edit(Request $request, $id)
    {

        // Получаем авторизованного пользователя
        $user = $request->user();

        // ПОЛУЧАЕМ ПОСТАВЩИКА ----------------------------------------------------------------------------------------------

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $dealer = Dealer::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $dealer);


        // ПОЛУЧАЕМ КОМПАНИЮ ------------------------------------------------------------------------------------------------

        $company_id = $dealer->company->id;

        $company = Company::with('location.city', 'schedules.worktimes', 'sector', 'services_types')
        ->findOrFail($company_id);
        // $this->authorize(getmethod(__FUNCTION__), $company);


        // ПОЛУЧАЕМ СЕКТОРА ------------------------------------------------------------------------------------------------

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('sectors', false, 'index');

        // Главный запрос
        $sectors = Sector::moderatorLimit($answer)
        ->orderBy('sort', 'asc')
        ->get(['id','name','category_status','parent_id'])
        ->keyBy('id')
        ->toArray();

        $services_types = [];
        foreach ($company->services_types as $service_type){
            $services_types[] = $service_type->id;
        }

        // Имя столбца
        $column = 'services_types_id';
        $request[$column] = $services_types;

        // Запрос для чекбокса - список типов услуг
        $services_types_query = ServicesType::get();

        // Контейнер для checkbox'а - инициируем
        $checkboxer['status'] = null;
        $checkboxer['entity_name'] = $this->entity_name;

        // Настраиваем checkboxer
        $services_types_checkboxer = addFilter(

            $checkboxer,                // Контейнер для checkbox'а
            $services_types_query,      // Коллекция которая будет взята
            $request,
            'Возможные типы услуг',     // Название чекбокса для пользователя в форме
            'services_types',           // Имя checkboxa для системы
            'id',                       // Поле записи которую ищем
            'services_types', 
            'internal-self-one',        // Режим выборки через связи
            'checkboxer'                // Режим: checkboxer или filter

        );

        // Функция отрисовки списка со вложенностью и выбранным родителем (Отдаем: МАССИВ записей, Id родителя записи, параметр блокировки категорий (1 или null), запрет на отображенеи самого элемента в списке (его Id))
        $sectors_list = get_select_tree($sectors, $company->sector_id, 1, null);

        if(isset($company->schedules->first()->worktimes)){
            $worktime_mass = $company->schedules->first()->worktimes->keyBy('weekday');
        }

        for($x = 1; $x<8; $x++){

            if(isset($worktime_mass[$x]->worktime_begin)){

                $worktime_begin = $worktime_mass[$x]->worktime_begin;
                $str_worktime_begin = secToTime($worktime_begin);
                $worktime[$x]['begin'] = $str_worktime_begin;

            } else {

                $worktime[$x]['begin'] = null;
            };

            if(isset($worktime_mass[$x]->worktime_interval)){

                $worktime_interval = $worktime_mass[$x]->worktime_interval;

                if(($worktime_begin + $worktime_interval) > 86400){

                    $str_worktime_interval = secToTime($worktime_begin + $worktime_interval - 86400);
                } else {

                    $str_worktime_interval = secToTime($worktime_begin + $worktime_interval);                       
                };

                $worktime[$x]['end'] = $str_worktime_interval;
            } else {

                $worktime[$x]['end'] = null;
            }
            
        };

        // Получаем список стран
        $countries_list = Country::get()->pluck('name', 'id');

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);
        return view('dealers.edit', compact('company', 'dealer', 'sectors_list', 'page_info', 'worktime', 'countries_list', 'services_types_checkboxer'));
    }


    public function update(SupplierRequest $request, $id)
    {

        // Получаем авторизованного пользователя
        $user = $request->user();

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $dealer = Dealer::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $dealer);

        $company_id = $dealer->company->id;

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_company = operator_right('companies', false, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $company = Company::with('location', 'schedules.worktimes')->findOrFail($company_id);

        // Скрываем бога
        $user_id = hideGod($user);

        // Обновляем локацию
        $company = update_location($request, $company);

        // Подключение политики
        // $this->authorize(getmethod(__FUNCTION__), $company);

        $company->name = $request->name;
        $company->alias = $request->alias;

        // $old_link_for_folder = $company->company_alias;
        // $new_link_for_folder = 'public/companies/' . $request->company_alias;
        // Переименовываем папку в файловой системе
        // Storage::move($old_link_for_folder, $new_link_for_folder);

        // Телефон
        $phones = add_phones($request, $company);

        $company->email = $request->email;

        $company->inn = $request->inn;
        $company->kpp = $request->kpp;
        $company->account_settlement = $request->account_settlement;
        $company->account_correspondent = $request->account_correspondent;
        $company->bank = $request->bank;

        if ($company->sector_id != $request->sector_id) {
            $company->sector_id = $request->sector_id;
        }

        // $company->director_user_id = Auth::user()->company_id;
        $company->save();

        // Если не существует расписания для компании - создаем его
        if($company->schedules->count() < 1){

            $schedule = new Schedule;
            $schedule->company_id = $user->company_id;
            $schedule->name = 'График работы для ' . $company->name;
            $schedule->description = null;
            $schedule->save();

            // Создаем связь расписания с компанией
            $schedule_entity = new ScheduleEntity;
            $schedule_entity->schedule_id = $schedule->id;
            $schedule_entity->entity_id = $company->id;
            $schedule_entity->entity = 'companies';
            $schedule_entity->save();

            $schedule_id = $schedule->id;
        } else {

            $schedule_id = $company->schedules->first()->id;
        };

        // Функция getWorktimes ловит все поля расписания из запроса и готовит к записи в worktimes
        $mass_time = getWorktimes($request, $schedule_id);

        // Удаляем все записи времени в worktimes для этого расписания
        $worktimes = Worktime::where('schedule_id', $schedule_id)->forceDelete();

        // Вставляем новое время в расписание
        DB::table('worktimes')->insert($mass_time);

        // Записываем связи: id-шники в таблицу companies_services_types
        $result = $company->services_types()->sync($request->services_types_id);

        return redirect('/admin/dealers');
    }


    public function destroy(Request $request,$id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $dealer = Dealer::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $dealer);

        if ($dealer) {

            $user = $request->user();

            // Скрываем бога
            $user_id = hideGod($user);
            $dealer->editor_id = $user_id;
            $dealer->save();

            $dealer = Dealer::destroy($id);

            // Удаляем компанию с обновлением
            if($dealer) {
                return redirect('/admin/dealers');

            } else {
                abort(403, 'Ошибка при удалении поставщика');
            }

        } else {
            abort(403, 'Поставщик не найдена');
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
