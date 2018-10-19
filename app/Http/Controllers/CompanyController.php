<?php

namespace App\Http\Controllers;

// Модели
use App\User;
use App\Company;
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
use App\ServicesType;
use App\Phone;

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

class CompanyController extends Controller
{

    // Сущность над которой производит операции контроллер
    protected $entity_name = 'companies';
    protected $entity_dependence = false;

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

        // -----------------------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -----------------------------------------------------------------------------------------------------------------------

        $companies = Company::with('author', 'director', 'location.city', 'sector', 'suppliers', 'manufacturers', 'main_phones')

        // ->suppliers($user->company_id)
        // ->manufacturers($user->company_id)
        ->moderatorLimit($answer)
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

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('sectors', false, 'index');

        // Главный запрос
        $sectors = Sector::moderatorLimit($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->orderBy('sort', 'asc')
        ->get(['id','name','category_status','parent_id'])
        ->keyBy('id')
        ->toArray();

        // Функция отрисовки списка со вложенностью и выбранным родителем (Отдаем: МАССИВ записей, Id родителя записи, параметр блокировки категорий (1 или null), запрет на отображенеи самого элемента в списке (его Id))
        $sectors_list = get_select_tree($sectors, null, 1, null);
        // dd($sectors_list);

        // Получаем список стран
        $countries_list = Country::get()->pluck('name', 'id');

        // // Получаем список стран
        // $services_types_list = ServicesType::get()->pluck('name', 'id');
        // dd($services_types_list);

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        // Запрос для чекбокса - список типов услуг
        $services_types_query = ServicesType::get();

        // Контейнер для checkbox'а - инициируем
        $checkboxer['status'] = null;
        $checkboxer['entity_name'] = $this->entity_name;

        // Настраиваем checkboxer
        $services_types_checkboxer = addFilter(

            $checkboxer,                // Контейнер для checkbox'а
            $services_types_query,        // Коллекция которая будет взята
            $request,
            'Возможные типы услуг',            // Название чекбокса для пользователя в форме
            'services_types',             // Имя checkboxa для системы
            'id',                       // Поле записи которую ищем
            'services_types', 
            'internal-self-one',        // Режим выборки через связи
            'checkboxer'                // Режим: checkboxer или filter

        );

        // Формируем пуcтой массив
        $worktime = [];
        for ($n = 1; $n < 8; $n++){$worktime[$n]['begin'] = null;$worktime[$n]['end'] = null;}

        // Сущность
            $entity = $this->entity_name;

        return view('companies.create', compact('company', 'sectors_list', 'page_info', 'worktime', 'countries_list', 'services_types_checkboxer', 'entity'));
    }

    public function store(CompanyRequest $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Company::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Смотрим компанию пользователя
        $company_id = $user->company_id;

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

        // Пишем локацию
        $location = new Location;
        $location->country_id = $request->country_id;
        $location->city_id = $request->city_id;
        $location->address = $request->address;
        $location->author_id = $user_id;
        $location->save();

        if ($location) {
            $location_id = $location->id;
        } else {
            abort(403, 'Ошибка записи адреса');
        }

        $company = new Company;
        $company->name = $request->name;
        $company->alias = $request->alias;

        $company->email = $request->email;

        // if(($request->extra_phone != NULL)&&($request->extra_phone != "")){
        //     $company->extra_phone = cleanPhone($request->extra_phone);
        // } else {$company->extra_phone = NULL;};

        $company->location_id = $location_id;

        $company->inn = $request->inn;
        $company->kpp = $request->kpp;
        $company->bank = $request->bank;
        $company->account_settlement = $request->account_settlement;
        $company->account_correspondent = $request->account_correspondent;

        $company->sector_id = $request->sector_id;
        $company->schedule_id = $schedule->id;

        // $company->director_user_id = $user->company_id;
        $company->author_id = $user_id;

        $company->save();

        // Если запись удачна - будем записывать связи
        if($company){

            // Телефон
            $phones = add_phones($request, $company);
            
            // Записываем связи: id-шники в таблицу Rooms
            if(isset($request->services_types_id)){

                $result = $company->services_types()->sync($request->services_types_id);               
            } else {
                $result = $company->services_types()->detach(); 
            };

        } else {
            abort(403, 'Ошибка записи компании');
        };


        // Указываем ее как поставщика

        $supplier = new Supplier;
        $supplier->company_id = $company_id;
        $supplier->contragent_id = $company->id;
        $supplier->save();

        // Создаем связь расписания с компанией
        $schedule_entity = new ScheduleEntity;
        $schedule_entity->schedule_id = $schedule->id;
        $schedule_entity->entity_id = $company->id;
        $schedule_entity->entity = 'companies';
        $schedule_entity->save();



        return redirect('/admin/companies');
        // return redirect('admin/companies');
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

    public function edit(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        $company = Company::with('location.city', 'schedules.worktimes', 'sector', 'services_types', 'main_phones', 'extra_phones')
        ->moderatorLimit($answer)
        ->findOrFail($id);

        // dd($company->main_phone);

        $this->authorize(getmethod(__FUNCTION__), $company);

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
            $services_types_query,        // Коллекция которая будет взята
            $request,
            'Возможные типы услуг',            // Название чекбокса для пользователя в форме
            'services_types',             // Имя checkboxa для системы
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

        // Сущность
        $entity = $this->entity_name;

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        return view('companies.edit', compact('company', 'sectors_list', 'page_info', 'worktime', 'countries_list', 'services_types_checkboxer', 'entity'));
    }

    public function update(CompanyRequest $request, $id)
    {

        // Получаем авторизованного пользователя
        $user = $request->user();

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $company = Company::with('location', 'schedules.worktimes')->moderatorLimit($answer)->findOrFail($id);

        // Скрываем бога
        $user_id = hideGod($user);

        // Пишем локацию
        $location = $company->location;
        if($location->city_id != $request->city_id) {
            $location->city_id = $request->city_id;
            $location->editor_id = $user_id;
            $location->save();
        }
        if($location->address != $request->address) {
            $location->address = $request->address;
            $location->editor_id = $user_id;
            $location->save();
        }
        if($location->country_id != $request->country_id) {
            $location->country_id = $request->country_id;
            $location->editor_id = $user_id;
            $location->save();
        }

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $company);

        $company->name = $request->name;

        if ($company->alias != $request->alias) {
            $company->alias = $request->alias;
        }
        
        // $old_link_for_folder = $company->company_alias;
        // $new_link_for_folder = 'public/companies/' . $request->company_alias;
        // Переименовываем папку в файловой системе
        // Storage::move($old_link_for_folder, $new_link_for_folder);

        // Телефон
        $phones = add_phones($request, $company);
        

        $company->email = $request->email;

        $company->inn = $request->inn;
        $company->kpp = $request->kpp;
        $company->bank = $request->bank;

        $company->account_settlement = $request->account_settlement;
        $company->account_correspondent = $request->account_correspondent;


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

        return redirect('/admin/companies');
    }


    public function destroy(Request $request,$id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $company = Company::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $company);



        if ($company) {

            $user = $request->user();

            // Скрываем бога
            $user_id = hideGod($user);
            $company->editor_id = $user_id;
            $company->save();

            // Удаляем локацию
            $company->location()->delete();

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

    // Сортировка
    public function ajax_sort(Request $request)
    {

        $i = 1;

        foreach ($request->companies as $item) {
            Company::where('id', $item)->update(['sort' => $i]);
            $i++;
        }
    }

    // Системная запись
    public function ajax_system_item(Request $request)
    {

        if ($request->action == 'lock') {
            $system = 1;
        } else {
            $system = null;
        }

        $item = Company::where('id', $request->id)->update(['system_item' => $system]);

        if ($item) {

            $result = [
                'error_status' => 0,
            ];  
        } else {

            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при обновлении статуса системной записи!'
            ];
        }
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    // Отображение на сайте
    public function ajax_display(Request $request)
    {

        if ($request->action == 'hide') {
            $display = null;
        } else {
            $display = 1;
        }

        $item = Company::where('id', $request->id)->update(['display' => $display]);

        if ($item) {

            $result = [
                'error_status' => 0,
            ];  
        } else {

            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при обновлении отображения на сайте!'
            ];
        }
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
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
