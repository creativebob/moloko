<?php

use App\Lead;
use App\Claim;
use Carbon\Carbon;

use App\Location;

use GuzzleHttp\Client;


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

        $lead_numbers['case'] = $lead_date->format('dmy') . '/' .  $serial_number . '/' . $user->liter;
        $lead_numbers['serial']  = $serial_number;           
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
function yandex_geocoder ($location) {

    // Если у локация не определялась, т.е. у нее не вписано количество ответов
    if ($location->answer_count == null) {

        // Формируем запрос в Яндекс Карты
        $request_params = [
            'geocode' => $location->city->name . ', ' .$location->address,
            'format' => 'json',
        ];
        // Преобразуем его в GET строку
        $params = http_build_query($request_params);
        // dd($get_params);
        // Отправляем
        $result = (file_get_contents('https://geocode-maps.yandex.ru/1.x/?' . $params));
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

        $res = json_decode($result);
        if (count($res->response->GeoObjectCollection->featureMember) == 1) {

            $string = $res->response->GeoObjectCollection->featureMember[0]->GeoObject->Point->pos;
            $coords = explode(' ', $string);
            $update_location = Location::whereId($location->id)->update(['longitude' => $coords[0], 'latitude' => $coords[1], 'parse_count' => 1, 'answer_count' => 1]);
        } else {
            $update_location = Location::whereId($location->id)->update(['answer_count' => count($res->response->GeoObjectCollection->featureMember)]);
        }
    }
}

// Добавление
function create_location($request) {

    // TODO: сюда умолчания из settings!
    $country_id = isset($request->country_id) ? $request->country_id : 1;
    $city_id = isset($request->city_id) ? $request->city_id : 1;

    $address = isset($request->address) ? $request->address : null;

    // Скрываем бога
    $user_id = hideGod($request->user());

    // Ищем или создаем локацию
    $location = Location::with('city')->firstOrCreate(compact('country_id', 'city_id', 'address'), ['author_id' => $user_id]);

    yandex_geocoder($location);

    return $location->id;

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

    // Скрываем бога
    $user_id = hideGod($request->user());

    // Ищем или создаем локацию
    $location = Location::with('city')->firstOrCreate(compact('country_id', 'city_id', 'address'), ['author_id' => $user_id]);

    // Если пришла другая локация, то переписываем
    if ($item->location_id != $location->id) {
        $item->location_id = $location->id;

        yandex_geocoder($location);
    }

    return $item;

}

?>
