<?php

use App\Lead;
use App\Claim;
use Carbon\Carbon;
use App\Location;
use App\ProcessesType;
use App\Bank;
use App\BankAccount;
use App\Company;
use App\Schedule;
use App\Worktime;
use App\Sector;
use App\RoleUser;
use App\Site;

// Транслитерация
use Illuminate\Support\Str;

use GuzzleHttp\Client;


// Получает экземпляр текущего сайта
function getSite() {

    $request = request();
    $domain = $request->getHost();
    $site = Site::where('domain', $domain)
        ->with([
            'pages_public',
            'filials'
        ])
        ->first();
    return $site;
}


// Если в функцию не передать ЛИДА, то будет сформирован номер
// для первого типа обращения на текущую дату
function getLeadNumbers($user, $lead = null) {

    // Создаем контейнер для хранения номеров заказа
    $lead_numbers = [];

    // Получаем умолчания, если не передан Лид
    if ($lead == null){
        $lead_date = Carbon::now();
        $lead_type_id = 1; // Обычное обращения (цель: покупка)
    } else {
        $lead_date = $lead->created_at;
        $lead_type_id = $lead->lead_type_id;
    }

    // Готовимся к выборке
    $answer_all_leads = operator_right('leads', 'true', 'index');

    // Смотрим какой тип обращения у лида которому мы должны сформировать номер
    // Если это ПРОСТОЕ ОБРАЩЕНИЕ, то:
    if($lead->lead_type_id == 1){

        // Делаем запрос: выбираем всех лидов на конкретный день по конкретному менеджеру
        // (пользователю, которого передали в качестве аргумента)
        $leads = Lead::moderatorLimit($answer_all_leads)
        ->companiesLimit($answer_all_leads)
        ->filials($answer_all_leads)
        ->where('manager_id', $user->id)
        ->where('lead_type_id', 1)
        ->whereDate('created_at', $lead_date->format('Y-m-d'))
        ->get();
    }

    // Если это ДИЛЕРСКОЕ ОБРАЩЕНИЕ, то:
    if($lead->lead_type_id == 2){

        // Делаем запрос: выбираем всех дилерских лидов на конкретный день
        $leads = Lead::moderatorLimit($answer_all_leads)
        ->companiesLimit($answer_all_leads)
        ->filials($answer_all_leads)
        ->where('lead_type_id', 2)
        ->whereDate('created_at', $lead_date->format('Y-m-d'))
        ->get();

    }

    // Если это СЕРВИСНОЕ ОБРАЩЕНИЕ, то:
    if($lead->lead_type_id == 3){

        // Делаем запрос: выбираем всех сервисных лидов на конкретный день
        $leads = Lead::moderatorLimit($answer_all_leads)
        ->companiesLimit($answer_all_leads)
        ->filials($answer_all_leads)
        ->where('lead_type_id', 3)
        ->whereDate('created_at', $lead_date->format('Y-m-d'))
        ->get();

    }

    // Формируем серийный номер. Берем самый большой серийник из базы и добавляем 1
    $serial_number = $leads->max('serial_number');
    if(empty($serial_number)){$serial_number = 0;};

    $serial_number = $serial_number + 1;


    // ЛОГИКА формирования номеров для разных типов: ----------------------------------------------------------------------

    // Создаем номер ОБЫЧНОГО обращения
    if($lead_type_id == 1){

//        if(!isset($_ENV['LEAD_NUMBER_LOGIC'])){ abort(403, 'Укажите в env файле переменную LEAD_NUMBER_LOGIC');}

//            dd(config('app.lead_number_logic'));
//        if(config('app.lead_number_logic') == 'simple'){

            $lead_numbers['case'] = $lead->id;
            $lead_numbers['serial']  = $serial_number;
//        } else {
//
//            $user_liter = $user->liter ?? $user->id;
//            $lead_numbers['case'] = $lead_date->format('dmy') . '/' .  $serial_number . '/' . $user_liter;
//            $lead_numbers['serial']  = $serial_number;
//        }

    }

    // Создаем номер ДИЛЕРСКОГО обращения
    if($lead_type_id == 2){

        $lead_numbers['case'] = $lead_date->format('dmy') . 'д' .  $serial_number;
        $lead_numbers['serial']  = $serial_number;
    }

    // Создаем номер СЕРВИСНОГО обращения
    if($lead_type_id == 3){

        $lead_numbers['case'] = $lead_date->format('dmy') . 'сц' .  $serial_number;
        $lead_numbers['serial']  = $serial_number;
    }

    // Отдаем результат
    return $lead_numbers;
}

function getClaimNumbers($user) {
    // Получаем из сессии необходимые данные (Функция находиться в Helpers)

    $today = Carbon::now();
    // $answer_all_claims = operator_right('claims', 'false', 'index');

    // $claims = Claim::companiesLimit($answer_all_claims)
    // ->companiesLimit($answer_all_claims)
    // ->whereDate('created_at', Carbon::today()->format('Y-m-d'))
    // ->get();

    $answer_all_claims = operator_right('claims', 'false', 'index');
    $claims_all = Claim::companiesLimit($answer_all_claims)
    ->companiesLimit($answer_all_claims)
    // ->whereDate('created_at', Carbon::today()->format('Y-m-d'))
    ->get();

    $serial_number = $claims_all->max('serial_number');

    if(empty($serial_number)){$serial_number = 0;};
    $serial_number = $serial_number + 1;

    // Контейнер для хранения номеров заказа
    $claim_numbers = [];

    // Создаем номера
    $claim_numbers['case'] = $today->format('dmy') . 'р' .  $serial_number;
    $claim_numbers['serial']  = $serial_number;
    // $claim_numbers['case'] = $today->format('dmy') . 'сц' .  $serial_number;
    // $claim_numbers['serial']  = $serial_number;

    return $claim_numbers;
}


// ---------------------------------------- Локации --------------------------------------------


// Функция получения широты и долготы в Яндекс Картах
function yandexGeocoder ($location) {

    if (isDomainAvailible('https://geocode-maps.yandex.ru')){

        // Если у локация не определялась, т.е. у нее не вписано количество ответов
        if ($location->answer_count == null) {

//            dd($location);

            // Формируем запрос в Яндекс Карты
            $request_params = [
                'geocode' => $location->city->name . ', ' .$location->address,
                'format' => 'json',
            ];
            // Преобразуем его в GET строку
            $params = http_build_query($request_params);
            // dd($get_params);
            // Отправляем
            // TODO - 16.10.19 - Не отрабатывает яндекс
//            $result = (file_get_contents('https://geocode-maps.yandex.ru/1.x/?' . $params));
            // dd($get_params);

            //     $client = new \GuzzleHttp\Client();
            // $res = $client->request('GET', 'https://geocode-maps.yandex.ru/1.x/?');
            // echo $res->getStatusCode();

            // $client = new Client(['base_uri' => 'https://geocode-maps.yandex.ru/1.x/']);
            // $request = $client->createRequest();
            // $request->getQuery()
            // ->set('geocode', $location->city->name . ', ' .$location->address)
            // ->set('format', 'json');

            // $result = $request->send();

//            $res = json_decode($result);
//            if (count($res->response->GeoObjectCollection->featureMember) == 1) {
//
//                $string = $res->response->GeoObjectCollection->featureMember[0]->GeoObject->Point->pos;
//                $coords = explode(' ', $string);
//                $update_location = Location::whereId($location->id)->update(['longitude' => $coords[0], 'latitude' => $coords[1], 'parse_count' => 1, 'answer_count' => 1]);
//            } else {
//                $update_location = Location::whereId($location->id)->update(['answer_count' => count($res->response->GeoObjectCollection->featureMember)]);
//            }
        }
    }
}

//возвращает true, если домен доступен, false если нет
function isDomainAvailible($domain)
{
    //проверка на валидность урла
    if(!filter_var($domain, FILTER_VALIDATE_URL)){
        return false;
    }
    //инициализация curl
    $curlInit = curl_init($domain);
    curl_setopt($curlInit,CURLOPT_CONNECTTIMEOUT,10);
    curl_setopt($curlInit,CURLOPT_HEADER,true);
    curl_setopt($curlInit,CURLOPT_NOBODY,true);
    curl_setopt($curlInit,CURLOPT_RETURNTRANSFER,true);
    //получение ответа
    $response = curl_exec($curlInit);
    curl_close($curlInit);
    if ($response) return true;
    return false;
}

/**
 * Define an inverse one-to-one or many relationship.
 *
 * @param  $request
 * @param  integer  $countryId
 * @param  integer  $cityId
 * @param  string  $address
 * @return variable;
 */

function create_location($request, $countryId = 1, $cityId = 1, $address = null, $zipCode = null)
{
    // Ищем или создаем локацию
    $location = Location::with('city')
        ->firstOrCreate([
            'country_id' => $request->country_id ?? $countryId,
            'city_id' => $request->city_id ?? $cityId,
            'address' => $request->address ?? $address,
            'zip_code' => $request->zip_code ?? $zipCode
        ], [
            'author_id' => hideGod($request->user())
        ]);

    yandexGeocoder($location);
//        dd($location);

    $location_id = $location->id;
    return $location_id;
}

// Обновление
function update_location($request, $item) {

// Обновляем локацию
    $item_location = $item->location;
// Проверяем страну, так как ее мы пока не выбираем
    if (isset($request->country_id)) {
        $country_id = ($item_location->country_id != $request->country_id) ? $request->country_id : $item_location->country_id;
    } else {
        $country_id = $item_location->country_id;
    }

    $city_id = ($item_location->city_id != $request->city_id) ? $city_id = $request->city_id : $item_location->city_id;
    $address = ($item_location->address != $request->address) ? $address = $request->address : $item_location->address;

    $zip_code = ($item_location->zip_code != $request->zip_code) ? $zip_code = $request->zip_code : $item_location->zip_code;

// Скрываем бога
    $user_id = hideGod($request->user());

// Ищем или создаем локацию
    $location = Location::with('city')
        ->firstOrCreate(compact(
            'country_id',
            'city_id',
            'address',
            'zip_code'
        ), [
            'author_id' => $user_id
        ]);

//        dd($location);

// Если пришла другая локация, то переписываем
    if ($item->location_id != $location->id) {
        $item->location_id = $location->id;

        yandexGeocoder($location);
    }

    return $item;

}


// Обновление
function addBankAccount($request, $company) {
    // Пришли ли с запросом имя банка, его БИК и рассчетный счет клиента,
    // которые так необходимы для создания нового аккаунта?
    if((isset($request->bank_bic))&&(isset($request->bank_name))&&(isset($request->account_settlement))){

        // Сохраняем в переменную наш БИК
        $bic = $request->bank_bic;
        $country_id = 1; // Россия
        $city_id = null; //
        $address = null;
        $legal_form_id = 4; // ПАО

        // Проверяем существуют ли у пользователя такие счета в указанном банке
        $cur_bank_account = BankAccount::whereNull('archive')
        ->where('account_settlement', '=' , $request->account_settlement)
        ->whereHas('bank', function($q) use ($bic){
            $q->where('bic', $bic);
        })->count();

        // Если такого счета нет, то:
        if($cur_bank_account == 0){

            // Создаем алиас для нового банка
            $company_alias = Str::slug($request->bank_name);
            $sector_bank_id = Sector::where('tag', 'bank')->firstOrFail()->id;
            $location_bank_id = create_location($request, $country_id, $city_id, $address);

            // Создаем новую компанию которая будет банком

            $company_bank = Company::firstOrCreate(['bic' => $request->bank_bic], ['name' => $request->bank_name, 'alias' => $company_alias, 'sector_id' => $sector_bank_id, 'location_id' => $location_bank_id, 'legal_form_id'=> $legal_form_id]);

            // Создаем банк, а если он уже есть - берем его ID
            $company_id = $request->user()->company_id ?? $company_bank->id;
            $bank = Bank::firstOrCreate(['company_id' => $company_id, 'bank_id' => $company_bank->id]);

            // Создаем новый банковский счёт
            $bank_account = new BankAccount;
            $bank_account->bank_id = $company_bank->id;
            $bank_account->holder_id = $company->id;
            $bank_account->company_id = $request->user()->company ? $request->user()->company->id : $company->id;

            $bank_account->account_settlement = $request->account_settlement;
            $bank_account->account_correspondent = $request->account_correspondent;
            $bank_account->author_id = $request->user()->id;
            $bank_account->save();

            return $bank_account ? true : false;

        }

    } else {

        // Не достаточно данных
        return false;
    }
}

// Обновление
function setSchedule($request, $company) {

    $schedule = $company->main_schedule;

    // Если не существует расписания для компании - создаем его
    if($schedule){

        $schedule_id = $schedule->id;

    } else {

        $schedule = new Schedule;

        if (isset($request->user()->company_id)) {
            $schedule->company_id = $request->user()->company->id;
        } else {
            $schedule->company_id = $company->id;
        }

        $schedule->name = 'График работы для ' . $company->name;
        $schedule->description = null;
        $schedule->save();

        $company->schedules()->attach($schedule->id, ['mode'=>'main']);
        $schedule_id = $schedule->id;

    };

        // Функция getWorktimes ловит все поля расписания из запроса и готовит к записи в worktimes
    $mass_time = getWorktimes($request, $schedule_id);

        // Удаляем все записи времени в worktimes для этого расписания
    $worktimes = Worktime::where('schedule_id', $schedule_id)->forceDelete();

        // Вставляем новое время в расписание
    DB::table('worktimes')->insert($mass_time);

        // Не достаточно данных
    return true;
}

// Обновление
function setProcessesType($request, $company) {

    // Записываем тип услуги
    if(isset($request->processes_types)){
        $result = $company->processes_types()->sync($request->processes_types);
    } else {
        $result = $company->processes_types()->detach();
    }

    return true;
}

// Обновление набора ролей
function setRoles($request, $user) {


    // Выполняем, только если данные пришли не из userfrofile!
    if(!isset($request->users_edit_mode)){

        if (isset($request->access)) {

             $delete = RoleUser::whereUser_id($user->id)->delete();
             $mass = [];
             foreach ($request->access as $string) {

                $item = explode(',', $string);

                if ($item[2] == 'null') {
                   $position = null;
               } else {
                   $position = $item[2];
               }

               $mass[] = [
                   'role_id' => $item[0],
                   'department_id' => $item[1],
                   'user_id' => $user->id,
                   'position_id' => $position,
               ];
           }

           DB::table('role_user')->insert($mass);

			// Успешно
            return true;

        } else {

			// Если удалили последнюю роль для должности и пришел пустой массив
            $delete = RoleUser::whereUser_id($user->id)->delete();

			// Успешно
            return true;
        }
    }
}


// Вписывание пользователю ролей из должности
function setRolesFromPosition($position, $department, $user) {

    $position->load('roles');
    $insert_array = [];
    foreach ($position->roles as $role) {
        $insert_array[$role->id] = [
            'department_id' => $department->id,
            'position_id' => $position->id,
        ];
    }

    $user->roles()->attach($insert_array);
    Log::info('Записали роли для юзера.');
}

// Отправка почты
function sendEmail($to_name, $to_email, $data) {

    // $to_name = 'Лехе Солтысяку';
    // $to_email = 'creativebob@yandex.ru';
    // $data = array('name'=>"Алексей", "body" => "Выполнен вхыод на страницу Компании");
    Mail::send('template_mails.simple', $data, function($message) use ($to_name, $to_email) {
        $message->to($to_email, $to_name)->subject('Тестирование отправки почты');
        $message->from('creativebobtwo@gmail.com','Artisans Web');
    });

}

function getSettings()
{
    return auth()->user()->company->settings;
}

/**
 * Получаем название документа через модель сущности
 *
 * @param $model
 * @return string|null
 */
function getDocumentNameByModel($model)
{
    $name = null;

    switch ($model) {
        case('App\Models\System\Documents\Production'):
            $name = 'Наряд на производство';
            break;

        case('App\Models\System\Documents\Consignment'):
            $name = 'Товарная накладная';
            break;

        case('App\Models\System\Documents\Estimate'):
            $name = 'Смета';
            break;
    }

    return $name;
}

/**
 * Получаем роут на редактирование документа через модель сущности
 *
 * @param $model
 * @return string|null
 */
function getDocumentRouteByModel($model)
{
    $route = null;

    switch ($model) {
        case('App\Models\System\Documents\Production'):
            $route = 'productions.edit';
            break;

        case('App\Models\System\Documents\Consignment'):
            $route = 'consignments.edit';
            break;

        case('App\Models\System\Documents\Estimate'):
            $route = 'leads.edit';
            break;
    }

    return $route;
}
