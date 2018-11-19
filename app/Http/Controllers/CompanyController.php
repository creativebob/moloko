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
use App\ServicesType;
use App\Phone;

// Транслитерация
use Transliterate;

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

        $companies = Company::with('author', 'director', 'location.city', 'sector', 'we_suppliers', 'we_manufacturers', 'we_dealers', 'main_phones')
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

        // Сущность
        $entity = $this->entity_name;

        return view('companies.create', compact('company', 'page_info', 'services_types_checkboxer', 'entity'));
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

        $company = new Company;
        $company->name = $request->name;
        $company->alias = $request->alias;

        $company->email = $request->email;

        // if(($request->extra_phone != NULL)&&($request->extra_phone != "")){
        //     $company->extra_phone = cleanPhone($request->extra_phone);
        // } else {$company->extra_phone = NULL;};

        // Добавляем локацию
        $company->location_id = create_location($request);
        // $company->location_id = $location->id;

        $company->inn = $request->inn;
        $company->kpp = $request->kpp;

        $company->ogrn = $request->ogrn;
        $company->okpo = $request->okpo;
        $company->okved = $request->okved;

        $company->legal_form_id = $request->legal_form_id;
        $company->sector_id = $request->sector_id;
        $company->schedule_id = $schedule->id;

        // $company->director_user_id = $user->company_id;
        $company->author_id = $user_id;

        $company->save();

        // Если запись удачна - будем записывать связи
        if($company){

            if((isset($request->bank_bic))&&(isset($request->bank_name))){

                // Сохраняем в переменную наш БИК
                $bic = $request->bank_bic;

                // Проверяем существуют ли у пользователя такие счета в указанном банке
                $cur_bank_account = BankAccount::whereNull('archive')
                ->where('account_settlement', '=' , $request->account_settlement)
                ->whereHas('bank', function($q) use ($bic){
                    $q->where('bic', $bic);
                })->count();

                // Если такого счета нет, то:
                if($cur_bank_account == 0){

                    // Создаем новый банковский счёт
                    $bank_account = new BankAccount;

                    // Создаем алиас для нового банка
                    $company_alias = Transliterate::make($request->bank_name, ['type' => 'url', 'lowercase' => true]);

                    // Создаем новую компанию которая будет банком
                    $company = Company::firstOrCreate(['bic' => $request->bank_bic], ['name' => $request->bank_name, 'alias' => $company_alias]);

                    // Создаем банк, а если он уже есть - берем его ID
                    $bank = Bank::firstOrCreate(['company_id' => $request->company_id, 'bank_id' => $company->id]);

                    $bank_account->bank_id = $company->id;
                    $bank_account->holder_id = $request->company_id;
                    $bank_account->company_id = $company_id;
                    $bank_account->account_settlement = $request->account_settlement;
                    $bank_account->account_correspondent = $request->account_correspondent;
                    $bank_account->author_id = $user->id;
                    $bank_account->save();
                }

            }


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

        $company = Company::with(
            'location.city', 
            'sector', 
            'services_types', 
            'main_phones', 
            'extra_phones', 
            'bank_accounts.bank')
        ->moderatorLimit($answer)
        ->findOrFail($id);

        $this->authorize(getmethod(__FUNCTION__), $company);

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

        // Сущность
        $entity = $this->entity_name;

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);
        return view('companies.edit', compact('company', 'page_info', 'services_types_checkboxer', 'entity'));
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
        $company = Company::with('location', 'schedules.worktimes')->moderatorLimit($answer)->findOrFail($id);

        // Обновляем локацию
        $company = update_location($request, $company);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $company);

        $company->name = $request->name;

        if ($company->alias != $request->alias) {
            $company->alias = $request->alias;
        }


        // Телефон
        $phones = add_phones($request, $company);
        $company->email = $request->email;

        $company->legal_form_id = $request->legal_form_id;
        $company->inn = $request->inn;
        $company->kpp = $request->kpp;
        $company->ogrn = $request->ogrn;
        $company->okpo = $request->okpo;
        $company->okved = $request->okved;

        if ($company->sector_id != $request->sector_id) {
            $company->sector_id = $request->sector_id;
        }

        // $company->director_user_id = Auth::user()->company_id;
        $company->save();
        $company_id = $company->id;

        if($company){

                // Сохраняем в переменную наш БИК
                $bic = $request->bank_bic;

                // Проверяем существуют ли у пользователя такие счета в указанном банке
                $cur_bank_account = BankAccount::whereNull('archive')
                ->where('account_settlement', '=' , $request->account_settlement)
                ->whereHas('bank', function($q) use ($bic){
                    $q->where('bic', $bic);
                })->count();

                // Если такого счета нет, то:
                if($cur_bank_account == 0){

                    // Создаем новый банковский счёт
                    $bank_account = new BankAccount;

                    // Создаем алиас для нового банка
                    $company_alias = Transliterate::make($request->bank_name, ['type' => 'url', 'lowercase' => true]);

                    // Создаем новую компанию которая будет банком
                    $company_bank = Company::firstOrCreate(['bic' => $request->bank_bic], ['name' => $request->bank_name, 'alias' => $company_alias]);

                    // Создаем банк, а если он уже есть - берем его ID
                    $bank = Bank::firstOrCreate(['company_id' => $request->company_id, 'bank_id' => $company_bank->id]);

                    $bank_account->bank_id = $company_bank->id;
                    $bank_account->holder_id = $company_id;
                    $bank_account->company_id = $user_company->id;

                    $bank_account->account_settlement = $request->account_settlement;
                    $bank_account->account_correspondent = $request->account_correspondent;
                    $bank_account->author_id = $user->id;
                    $bank_account->save();
                }

        }

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

        // dd($mass_time);

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
            // $company->location()->delete();

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
            return $company->name;};
        }

    }
