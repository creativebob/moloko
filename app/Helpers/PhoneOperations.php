<?php
use App\Phone;

// Функция записи/обновления основного/дополнительных номеров телефона, принимает $request (в нем лежат основной номер и массив с дополнительными) и экземпляр модели, чтоб через нее делать связь
function add_phones($request, $item) {

    // Телефон
	if (isset($request->main_phone)) {

        // Если у записи нет телефона
		if (isset($item->main_phone->phone)) {

            // Если пришедший номер не равен существующему
			if ($item->main_phone->phone != cleanPhone($request->main_phone)) {
                // dd($request->main_phone);

                // Отправляем старый номер в архив
				$old_phone = Phone::where('phone', $item->main_phone->phone)->first();
				$item->main_phone()->updateExistingPivot($old_phone->id, ['archive' => 1]);

                // Пишем или ищем новый и создаем связь
				$phone = Phone::firstOrCreate(['phone' => cleanPhone($request->main_phone)]);
				$item->phones()->attach($phone->id, ['main' => 1]);
			}
		} else {
            // Если номера нет, пишем или ищем новый и создаем связь
			$phone = Phone::firstOrCreate(['phone' => cleanPhone($request->main_phone)]);
			$item->phones()->attach($phone->id, ['main' => 1]); 
		}

        // Если пришли дополнительные номера
		if (isset($request->extra_phones)) {
			if (count($request->extra_phones) > 0) {
            // dd($request->extra_phones);

            // Берем Id пришедших телефонов, или создаем их, если их нет в базе
				$request_extra_phones = [];
				foreach ($request->extra_phones as $extra_phone) {
					if ($extra_phone != null) {
                    // $mass_extra_phones[] = cleanPhone($extra_phone);
						$phone = Phone::firstOrCreate(['phone' => cleanPhone($extra_phone)]);
						$request_extra_phones[] = $phone->id;
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
	}
}

?>