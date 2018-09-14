<?php

use App\Lead;
use Carbon\Carbon;

function getLeadNumbers($user) {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)

	$today = Carbon::now();
        $answer_all_leads = operator_right('leads', 'true', 'index');

        $leads = Lead::moderatorLimit($answer_all_leads)
        ->companiesLimit($answer_all_leads)
        ->filials($answer_all_leads) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
        ->manager($user)
        ->whereDay('created_at', Carbon::today()->format('d'))
        ->get();

        $serial_number = $leads->max('serial_number');

        if(empty($serial_number)){$serial_number = 0;};

        $serial_number = $serial_number + 1;

        // Контейнер для хранения номеров заказа
        $lead_numbers = [];

        // Создаем номера
        $lead_numbers['case'] = $today->format('dmy') . '/' .  $serial_number . '/' . $user->liter;
        $lead_numbers['serial']  = $serial_number;

        return $lead_numbers;
}


?>
