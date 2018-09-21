<?php

use App\Lead;
use App\Claim;
use Carbon\Carbon;

function getLeadNumbers($user) {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)

	$today = Carbon::now();
        $answer_all_leads = operator_right('leads', 'true', 'index');

        $leads = Lead::moderatorLimit($answer_all_leads)
        ->companiesLimit($answer_all_leads)
        ->filials($answer_all_leads) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
        ->manager($user)
        ->whereDate('created_at', Carbon::today()->format('Y-m-d'))
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

function getClaimNumbers($user) {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)

        $today = Carbon::now();
        // $answer_all_claims = operator_right('claims', 'false', 'index');

        // $claims = Claim::companiesLimit($answer_all_claims)
        // ->companiesLimit($answer_all_claims)
        // ->whereDate('created_at', Carbon::today()->format('Y-m-d'))
        // ->get();

        $answer_all_claims = operator_right('claims', 'false', 'index');
        $claims_count = Claim::companiesLimit($answer_all_claims)
        ->companiesLimit($answer_all_claims)
        ->whereDate('created_at', Carbon::today()->format('Y-m-d'))
        ->count();

        $claims_count = $claims_count + 1;

        // dd($claims_count);

        // $serial_number = $claims->max('serial_number');
        // if(empty($serial_number)){$serial_number = 0;};
        // $serial_number = $serial_number + 1;

        // Контейнер для хранения номеров заказа
        $claim_numbers = [];

        // Создаем номера
        $claim_numbers['case'] = $today->format('dmy') . 'сц' .  $claims_count;
        $claim_numbers['serial']  = $claims_count;
        // $claim_numbers['case'] = $today->format('dmy') . 'сц' .  $serial_number;
        // $claim_numbers['serial']  = $serial_number;

        return $claim_numbers;
}

?>
