<?php

namespace App\Http\Controllers;

// Модели
use App\User;
use App\Bank;
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
use App\ProcessesType;
use App\Phone;

// Модели которые отвечают за работу с правами + политики
use App\Policies\CompanyPolicy;
use App\Policies\BankPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

// Запросы и их валидация
use Illuminate\Http\Request;
use App\Http\Requests\System\CompanyRequest;
use App\Http\Requests\System\SupplierRequest;

// Общие классы
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BankController extends Controller
{

    // Сущность над которой производит операции контроллер
    protected $entity_name = 'banks';
    protected $entity_dependence = false;

    public function index(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Bank::class);

        // Получаем авторизованного пользователя
        $user = $request->user();

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // -------------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -------------------------------------------------------------------------------------------------------------

        $banks = Bank::with('author', 'company.main_phones')
        ->whereNotNull('company_id')
        ->moderatorLimit($answer)
        ->filter($request, 'city_id', 'location')
        ->filter($request, 'sector_id')
        ->booklistFilter($request)
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->paginate(30);

        // dd($banks);

        // -----------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ------------------------------------------------------------------------------
        // -----------------------------------------------------------------------------------------------------------

        $filter = setFilter($this->entity_name, $request, [
            'city',                 // Город
            'sector',               // Направление деятельности
            'booklist'              // Списки пользователя
        ]);

        // Окончание фильтра -----------------------------------------------------------------------------------------



        // Инфо о странице
        $pageInfo = pageInfo($this->entity_name);

        return view('banks.index', compact('banks', 'pageInfo', 'filter', 'user'));
    }

    public function create(Request $request)
    {

        //Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Bank::class);
        // $this->authorize(getmethod(__FUNCTION__), Company::class);

        // Создаем новый экземляр компании
        $bank = new Bank;

        // Создаем новый экземляр поставщика
        $company = new Company;

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('sectors', false, 'index');

        // Главный запрос
        $sectors = Sector::moderatorLimit($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->orderBy('sort', 'asc')
        ->get(['id','name','parent_id'])
        ->keyBy('id')
        ->toArray();

        // Функция отрисовки списка со вложенностью и выбранным родителем (Отдаем: МАССИВ записей, Id родителя записи, параметр блокировки категорий (1 или null), запрет на отображенеи самого элемента в списке (его Id))
        $sectors_list = get_select_tree($sectors, null, 1, null);

        // Получаем список стран
        $countries_list = Country::get()->pluck('name', 'id');

        // // Получаем список стран
        // $processes_types_list = ProcessesType::get()->pluck('name', 'id');

        // Инфо о странице
        $pageInfo = pageInfo($this->entity_name);

        // Запрос для чекбокса - список типов услуг
        $processes_types_query = ProcessesType::get();

        // Контейнер для checkbox'а - инициируем
        $checkboxer['status'] = null;
        $checkboxer['entity_name'] = $this->entity_name;

        // Настраиваем checkboxer
        $processes_types_checkboxer = addFilter(

            $checkboxer,                // Контейнер для checkbox'а
            $processes_types_query,      // Коллекция которая будет взята
            $request,
            'Возможные типы услуг',     // Название чекбокса для пользователя в форме
            'processes_types',           // Имя checkboxa для системы
            'id',                       // Поле записи которую ищем
            'processes_types',
            'internal-self-one',        // Режим выборки через связи
            'checkboxer'                // Режим: checkboxer или filter

        );

        // Формируем пуcтой массив
        $worktime = [];
        for ($n = 1; $n < 8; $n++){$worktime[$n]['begin'] = null;$worktime[$n]['end'] = null;}

        return view('banks.create', compact('company', 'bank', 'sectors_list', 'pageInfo', 'worktime', 'countries_list', 'processes_types_checkboxer'));
    }

    public function store(CompanyRequest $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Bank::class);
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

        $bank = new Company;
        $bank->name = $request->name;
        $bank->alias = $request->alias;

        $bank->email = $request->email;

        // Добавляем локацию
        $bank->location_id = create_location($request);

        $bank->inn = $request->inn;
        $bank->kpp = $request->kpp;
        $bank->bic = $request->bic;

        $bank->account_settlement = $request->account_settlement;
        $bank->account_correspondent = $request->account_correspondent;

        $bank->sector_id = $request->sector_id;
        $bank->schedule_id = $schedule->id;

        // $company->director_user_id = $user->company_id;
        $bank->author_id = $user->id;

        $bank->save();

        // Если запись удачна - будем записывать связи
        if($bank){

            // Записываем компанию как банк
            $company->banks()->attach($bank->id);

            // Телефон
            $phones = add_phones($request, $bank);

            // Записываем связи: id-шники в таблицу Rooms
            if(isset($request->processes_types_id)){

                $result = $bank->processes_types()->sync($request->processes_types_id);
            } else {
                $result = $bank->processes_types()->detach();
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

        return redirect('/admin/banks');
        // return redirect('admin/companies');
    }


    public function show($id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $bank = Bank::moderatorLimit($answer)->find($id);

        // Подключение политики
        $this->authorize('view', $bank);
        return view('banks.show', compact('bank'));
    }


    public function edit(Request $request, $id)
    {

        // Получаем авторизованного пользователя
        $user = $request->user();

        // ПОЛУЧАЕМ ПОСТАВЩИКА ----------------------------------------------------------------------------------------------

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $bank = Bank::moderatorLimit($answer)->find($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $bank);


        // ПОЛУЧАЕМ КОМПАНИЮ ------------------------------------------------------------------------------------------------

        $company_id = $bank->company->id;

        $company = Company::with('location.city', 'schedules.worktimes', 'sector', 'processes_types')
        ->find($company_id);
        // $this->authorize(getmethod(__FUNCTION__), $company);


        // ПОЛУЧАЕМ СЕКТОРА ------------------------------------------------------------------------------------------------

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('sectors', false, 'index');

        // Главный запрос
        $sectors = Sector::moderatorLimit($answer)
        ->orderBy('sort', 'asc')
        ->get(['id','name','parent_id'])
        ->keyBy('id')
        ->toArray();

        $processes_types = [];
        foreach ($company->processes_types as $processes_type){
            $processes_types[] = $processes_type->id;
        }

        // Имя столбца
        $column = 'processes_types_id';
        $request[$column] = $processes_types;

        // Запрос для чекбокса - список типов услуг
        $processes_types_query = ProcessesType::get();

        // Контейнер для checkbox'а - инициируем
        $checkboxer['status'] = null;
        $checkboxer['entity_name'] = $this->entity_name;

        // Настраиваем checkboxer
        $processes_types_checkboxer = addFilter(

            $checkboxer,                // Контейнер для checkbox'а
            $processes_types_query,      // Коллекция которая будет взята
            $request,
            'Возможные типы услуг',     // Название чекбокса для пользователя в форме
            'processes_types',           // Имя checkboxa для системы
            'id',                       // Поле записи которую ищем
            'processes_types',
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
        $pageInfo = pageInfo($this->entity_name);
        return view('banks.edit', compact('company', 'bank', 'sectors_list', 'pageInfo', 'worktime', 'countries_list', 'processes_types_checkboxer'));
    }


    public function update(SupplierRequest $request, $id)
    {

        // Получаем авторизованного пользователя
        $user = $request->user();

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $bank = Bank::moderatorLimit($answer)->find($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $bank);

        $company_id = $bank->company->id;

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_company = operator_right('companies', false, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $company = Company::with('location', 'schedules.worktimes')->find($company_id);

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
        $company->bic = $request->bic;
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

        // Записываем связи: id-шники в таблицу companies_processes_types
        $result = $company->processes_types()->sync($request->processes_types_id);

        return redirect('/admin/banks');
    }


    public function destroy(Request $request,$id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $bank = Bank::moderatorLimit($answer)->find($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $bank);

        if ($bank) {

            $user = $request->user();

            // Скрываем бога
            $user_id = hideGod($user);
            $bank->editor_id = $user_id;
            $bank->save();

            $bank = Bank::destroy($id);

            // Удаляем компанию с обновлением
            if($bank) {
                return redirect('/admin/banks');

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
