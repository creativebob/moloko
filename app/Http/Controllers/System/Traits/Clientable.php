<?php

namespace App\Http\Controllers\System\Traits;

use App\Client;
use App\Models\System\Documents\Estimate;
use Carbon\Carbon;
use Illuminate\Support\Facades\Request;

trait Clientable
{

    public function getClientUser($userId)
    {
        $client = Client::firstOrCreate([
            'clientable_id' => $userId,
            'clientable_type' => 'App\User',
        ]);

        return $client;
    }

    public function getClientCompany($companyId)
    {
        $client = Client::firstOrCreate([
            'clientable_id' => $companyId,
            'clientable_type' => 'App\Company',
        ]);

        return $client;
    }

    /**
     * @param $estimate
     * @return mixed
     */
    public function setClientIndicators($estimate)
    {

        $client = $estimate->client;
        $data = [];

        $data['first_order_date'] = isset($client->first_order_date) ? Carbon::parse($client->first_order_date) : Carbon::parse($estimate->created_at);
        $data['last_order_date'] = Carbon::parse($estimate->created_at);

//        $data['first_order_date'] = isset($client->first_order_date) ? $client->first_order_date : today();
//        $data['last_order_date'] = today();
        $data['orders_count'] = $client->orders_count + 1;
//        dd($data);


        // TODO - 23.04.20 - Если разница меньше 1 месяца, то вписываем 1 месяц в секундах
//        $diff = $data['last_order_date']->diff($data['first_order_date']);
//        $diffa = ($diff->format('%y') * 12) + $diff->format('%m');
//        if ($diffa == 0) {
//            $diffa = 1;
//        }

        $diffInMonths = $data['first_order_date']->diffInMonths($data['last_order_date']);
        // TODO - 18.09.20 - Меняем lifetime на 1 год от даты последнего заказа
        $diffInMonths += 12;
//        if ($diffInMonths == 0) {
//            $diffInMonths = 1;
//        }
        $data['lifetime'] = $diffInMonths;

        // TODO - 18.09.20 - Изменения в частоте
        $factLifetime = $data['first_order_date']->diffInMonths(today());
        if ($factLifetime == 0) {
            $factLifetime = 1;
        }
        $data['purchase_frequency'] = $data['orders_count'] / $factLifetime;
//        $data['purchase_frequency'] = $data['orders_count'] / $data['lifetime'];

        $data['ait'] = 1 / $data['purchase_frequency'];

        $total = Estimate::where([
            'client_id' => $client->id,
        ])
            ->whereNotNull('conducted_at')
            ->sum('total');
        $data['customer_equity'] = $total;

        $data['average_order_value'] = $data['customer_equity'] / $data['orders_count'];
        $data['customer_value'] = $data['average_order_value'] * $data['purchase_frequency'];

        // TODO - 22.04.20 - Lifetime перевести в месяца
        $data['ltv'] = $data['lifetime'] * $data['average_order_value'] * $data['purchase_frequency'];

        // TODO - 22.04.20 - Пока нет промоакций
        $data['use_promo_count'] = 0;
        $data['promo_rate'] = $data['use_promo_count'] / $data['orders_count'];

        $data['is_lost'] = false;

//        dd($data);

        $client->update($data);

        return $data;
    }

}
