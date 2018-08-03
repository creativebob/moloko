<?php

namespace App\Http\Controllers;

// Модели
use App\User;
use App\Manufacturer;
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
use App\Country;
use App\ServicesType;

// Модели которые отвечают за работу с правами + политики
use App\Policies\CompanyPolicy;
use App\Policies\ManufacturerPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

// Запросы и их валидация
use Illuminate\Http\Request;
use App\Http\Requests\CompanyRequest;
use App\Http\Requests\ManufacturerRequest;

// Прочие необходимые классы
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ManufacturerController extends Controller
{

    // Сущность над которой производит операции контроллер
    protected $entity_name = 'manufacturers';
    protected $entity_dependence = false;

    public function index(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Manufacturer::class);

        // Получаем авторизованного пользователя
        $user = $request->user();

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // -------------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -------------------------------------------------------------------------------------------------------------

        $manufacturers = Manufacturer::with('author', 'company')
        ->where('company_id', '!=', null)
        ->moderatorLimit($answer)
        ->booklistFilter($request)
        ->orderBy('sort', 'asc')
        ->paginate(30);


        $filter_query = Manufacturer::moderatorLimit($answer)->get();
        $filter['status'] = null;

        // $filter = addFilter($filter, $filter_query, $request, 'Выберите город:', 'city', 'city_id', 'location', 'external-id-one');

        // Добавляем данные по спискам (Требуется на каждом контроллере)
        $filter = addBooklist($filter, $filter_query, $request, $this->entity_name);

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        return view('manufacturers.index', compact('manufacturers', 'page_info', 'filter', 'user'));
    }

    public function create(Request $request)
        {

        //Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Manufacturer::class);
        $this->authorize(getmethod(__FUNCTION__), Company::class);

        // Создаем новый экземляр компании 
        $manufacturer = new Manufacturer;

        // Создаем новый экземляр поставщика
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

        // Получаем список стран
        $countries_list = Country::get()->pluck('name', 'id');

        // // Получаем список стран
        // $services_types_list = ServicesType::get()->pluck('name', 'id');

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        // Формируем checkboxer со списком типов услуг
        $services_types_query = ServicesType::get();
        $filter['status'] = null;
        $services_types_checkboxer = addFilter($filter, $services_types_query, $request, 'Возможные типы услуг', 'services_types', 'id', 'services_types', 'internal-self-one');

        // Формируем пуcтой массив
        $worktime = [];
        for ($n = 1; $n < 8; $n++){$worktime[$n]['begin'] = null;$worktime[$n]['end'] = null;}

        return view('manufacturers.create', compact('company', 'manufacturer', 'sectors_list', 'page_info', 'worktime', 'countries_list', 'services_types_checkboxer'));
    }

    public function store(CompanyRequest $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Manufacturer::class);
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

        $company->phone = cleanPhone($request->phone);
        $company->email = $request->email;

        if(($request->extra_phone != NULL)&&($request->extra_phone != "")){
            $company->extra_phone = cleanPhone($request->extra_phone);
        } else {$company->extra_phone = NULL;};

        $company->location_id = $location_id;

        $company->inn = $request->inn;
        $company->kpp = $request->kpp;
        $company->account_settlement = $request->account_settlement;
        $company->account_correspondent = $request->account_correspondent;

        $company->sector_id = $request->sector_id;
        $company->schedule_id = $schedule->id;

        // $company->director_user_id = $user->company_id;
        $company->author_id = $user->id;

        $company->save();

        // Если запись удачна - будем записывать связи
        if($company){

            // Записываем связи: id-шники в таблицу Rooms
            if(isset($request->services_types_id)){
                
                $result = $company->services_types()->sync($request->services_types_id);               
            } else {
                $result = $company->services_types()->detach(); 
            };

        } else {
            abort(403, 'Ошибка записи компании');
        };

        $manufacturer = new Manufacturer;
        $manufacturer->company_id = $company_id;
        $manufacturer->contragent_id = $company->id;
        $manufacturer->save();

        // Создаем связь расписания с компанией
        $schedule_entity = new ScheduleEntity;
        $schedule_entity->schedule_id = $schedule->id;
        $schedule_entity->entity_id = $company->id;
        $schedule_entity->entity = 'companies';
        $schedule_entity->save();

        return redirect('/admin/manufacturers');
        // return redirect('admin/companies');
    }


    public function show($id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $manufacturer = Manufacturer::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize('view', $manufacturer);
        return view('manufacturers.show', compact('manufacturer'));
    }


    public function edit(Request $request, $id)
    {

        // Получаем авторизованного пользователя
        $user = $request->user();

        // ПОЛУЧАЕМ ПОСТАВЩИКА ----------------------------------------------------------------------------------------------

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $manufacturer = Manufacturer::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $manufacturer);



        // ПОЛУЧАЕМ КОМПАНИЮ ------------------------------------------------------------------------------------------------

        $company_id = $manufacturer->company->id;

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_company = operator_right('company', false, getmethod(__FUNCTION__));

        $company = Company::with('location.city', 'schedules.worktimes', 'sector', 'services_types')
        ->moderatorLimit($answer_company)
        ->findOrFail($company_id);

        $this->authorize(getmethod(__FUNCTION__), $company);


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

        $services_types_query = ServicesType::get();

        $filter['status'] = null;
        $services_types_checkboxer = addFilter($filter, $services_types_query, $request, 'Возможные типы услуг', 'services_types', 'id', 'services_types', 'internal-self-one');

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
        return view('manufacturers.edit', compact('company', 'manufacturer', 'sectors_list', 'page_info', 'worktime', 'countries_list', 'services_types_checkboxer'));
    }


    public function update(ManufacturerRequest $request, $id)
    {

        // Получаем авторизованного пользователя
        $user = $request->user();

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $manufacturer = Manufacturer::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $manufacturer);


        $company_id = $manufacturer->company->id;

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_company = operator_right('companies', false, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $company = Company::with('location', 'schedules.worktimes')->moderatorLimit($answer_company)->findOrFail($company_id);

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
        $company->alias = $request->alias;

        // $old_link_for_folder = $company->company_alias;
        // $new_link_for_folder = 'public/companies/' . $request->company_alias;
        // Переименовываем папку в файловой системе
        // Storage::move($old_link_for_folder, $new_link_for_folder);

        $company->phone = cleanPhone($request->phone);
        $company->email = $request->email;

        if(($request->extra_phone != NULL)&&($request->extra_phone != "")){
            $company->extra_phone = cleanPhone($request->extra_phone);
        } else {$company->extra_phone = NULL;};

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

        return redirect('/admin/manufacturers');
    }


    public function destroy(Request $request,$id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $manufacturer = Manufacturer::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $manufacturer);

        if ($manufacturer) {

            $user = $request->user();

            // Скрываем бога
            $user_id = hideGod($user);
            $manufacturer->editor_id = $user_id;
            $manufacturer->save();

            $manufacturer = Manufacturer::destroy($id);

            // Удаляем компанию с обновлением
            if($manufacturer) {
                return redirect('/admin/manufacturers');

            } else {
                abort(403, 'Ошибка при удалении поставщика');
            }

        } else {
            abort(403, 'Поставщик не найдена');
        }
    }

    // Сортировка
    public function Manufacturers_sort(Request $request)
    {
        $result = '';
        $i = 1;
        foreach ($request->manufacturers as $item) {

            $manufacturers = Manufacturer::findOrFail($item);
            $manufacturers->sort = $i;
            $manufacturers->save();
            $i++;
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