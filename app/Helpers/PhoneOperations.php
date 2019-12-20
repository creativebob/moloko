<?php

use App\Phone;
use App\Client;

// Функция записи/обновления основного/дополнительных номеров телефона, принимает $request 
// (в нем лежат основной номер и массив с дополнительными) и экземпляр модели, чтоб через нее делать связь

function add_phones($request, $item) {

    // Телефон
	if (isset($request->main_phone)) {

        // Если пришли дополнительные номера
		if (isset($request->extra_phones)) {
			if (count($request->extra_phones) > 0) {
            	// dd($request->extra_phones);

            	// Берем Id пришедших телефонов, или создаем их, если их нет в базе
				$request_extra_phones = [];
				foreach ($request->extra_phones as $extra_phone) {
					if ($extra_phone != null) {
                    	// $mass_extra_phones[] = cleanPhone($extra_phone);

						if (cleanPhone($extra_phone) != cleanPhone($request->main_phone)) {

							$phone = Phone::firstOrCreate([
								'phone' => cleanPhone($extra_phone)
							], [
								'crop' => substr(cleanPhone($extra_phone), -4),
							]);
							$request_extra_phones[] = $phone->id;
						}
					}
				}
            	// dd($request_extra_phones);

            	// Берем дополнительные телефоны записи
				$item_extra_phones = [];
				foreach ($item->extra_phones as $extra_phone) {
					$item_extra_phones[] = $extra_phone->id;
				}
            	// dd($item_extra_phones);

            	// Ставим удаленным (не пришедшим номерам) статус архива
				$mass_diff = array_diff($item_extra_phones, $request_extra_phones);
            	// dd($mass_diff);
				if (count($mass_diff) > 0) {
					foreach ($mass_diff as $insert) {
						$item->phones()->updateExistingPivot($insert, ['archive' => 1]);
					}
				}

            	// Пишем новые номера
				$mass_new = array_diff($request_extra_phones, $item_extra_phones);
            	// dd($mass_new);
				if (count($mass_new) > 0) {
					$item->extra_phones()->attach($mass_new);
				}
			}
		}

		// Если у записи нет телефона
		if (isset($item->main_phone->phone)) {

            // Если пришедший номер не равен существующему
			if ($item->main_phone->phone != cleanPhone($request->main_phone)) {
                // dd($request->main_phone);

                // Отправляем старый номер в архив
				$old_phone = Phone::where('phone', $item->main_phone->phone)->first();
				$item->main_phones()->updateExistingPivot($old_phone->id, ['archive' => 1]);

                // Пишем или ищем новый и создаем связь
				$phone = Phone::firstOrCreate(
					['phone' => cleanPhone($request->main_phone)
				], [
					'crop' => substr(cleanPhone($request->main_phone), -4),
				]);
				// dd($phone);
				$item->phones()->attach($phone->id, ['main' => 1]);
			}
		} else {
            // Если номера нет, пишем или ищем новый и создаем связь
			$phone = Phone::firstOrCreate(
				['phone' => cleanPhone($request->main_phone)
			], [
				'crop' => substr(cleanPhone($request->main_phone), -4),
			]);
			$item->phones()->attach($phone->id, ['main' => 1]);
		}
	}
}


// Функция поиска пользователя компании по номеру телефона

function checkPhoneUserForCompany($phone_search, $company) {

	Log::info('Функция поиска пользователя с указанным номером в рамках компании');
	$phone = Phone::where('phone', cleanPhone($phone_search))->first();

	if($phone){
		$users_owners = $phone->user_owner->where('company_id', $company->id);
		if($users_owners->first() !== null){
			$user = $users_owners->first();
			Log::info('Нашли пользователей для компании: ' . $user->name ?? 'Имя не указано');
		}
	}
	
	return $user ?? null;
}

function checkPhoneUserForSite($phone_from_site, $site) {

	if(!$site){abort('Функции не передан экзмепляр сайта');};
	Log::info('Проверяем телефон пользователя - есть ли такой номер в базе именно для этого сайта?');

	// Ищем телефон в базе телефонов
	$phone = Phone::where('phone', cleanPhone($phone_from_site))->first();

	if(!empty($phone)){

		Log::info('Нашли телефон в общей базе');

		$result = $phone->user_owner->where('site_id', $site->id)->where('company_id', $site->company->id);

		if($result->first() !== null){
			Log::info('Нашли телефон в связке с текущим сайтом');

			$user = $result->first();
			Log::info($user->name ?? 'Имя не указано');

		} else {
			$user = null;
			Log::info('А вот в связке с текущим сайтом - не нашли');
		}

	} else {$user = null;};
	
	return $user;
}

// Отправка СМС через API smsru
function sendSms($company, $phone, $msg) {

		Log::info('Запущена функция отправки:');

		Log::info('Компания: ' . $company . ', телефон: ' . $phone . ', сообщение: ' . $msg);
        $ch = curl_init("https://sms.ru/sms/send");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array(
            "api_id" => $company->accounts->where('alias', 'smssend')->first()->api_token,
            "to" => $phone, // До 100 штук до раз
            "msg" => $msg,
            "json" => 1 // Для получения более развернутого ответа от сервера
        )));

        $body = curl_exec($ch);
        curl_close($ch);

        $json = json_decode($body);


        // if ($json) { // Получен ответ от сервера
        //     print_r($json); // Для дебага
        //     if ($json->status == "OK") { // Запрос выполнился
        //         foreach ($json->sms as $phone => $data) { // Перебираем массив СМС сообщений
        //             if ($data->status == "OK") { // Сообщение отправлено
        //                 echo "Сообщение на номер $phone успешно отправлено. ";
        //                 echo "ID сообщения: $data->sms_id. ";
        //                 echo "";
        //             } else { // Ошибка в отправке
        //                 echo "Сообщение на номер $phone не отправлено. ";
        //                 echo "Код ошибки: $data->status_code. ";
        //                 echo "Текст ошибки: $data->status_text. ";
        //                 echo "";
        //             }
        //         }
        //         echo "Баланс после отправки: $json->balance руб.";
        //         echo "";
        //     } else { // Запрос не выполнился (возможно ошибка авторизации, параметрах, итд...)
        //         echo "Запрос не выполнился. ";
        //         echo "Код ошибки: $json->status_code. ";
        //         echo "Текст ошибки: $json->status_text. ";
        //     }
        // } else {

        //     echo "Запрос не выполнился. Не удалось установить связь с сервером. ";

        // }
}


?>