<?php

namespace App\Http\Controllers\System\Traits;

use App\Client;
use App\Estimate;
use Carbon\Carbon;
use Illuminate\Support\Facades\Request;

trait Clientable
{

    public function checkClientUser($user_id)
    {
        $client = Client::firstOrCreate([
            'clientable_id' => $user_id,
            'clientable_type' => 'App\User',
            'company_id' => auth()->user()->company_id
        ]);

        return $client;
    }

    /**
     * @param $estimate
     * @return mixed
     */
    public function setIndicators($estimate)
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
//
//
//        if ($diffa == 0) {
//            $diffa = 1;
//        }

        $diffInMonths = $data['first_order_date']->diffInMonths($data['last_order_date']);
        if ($diffInMonths == 0) {
            $diffInMonths = 1;
        }
        $data['lifetime'] = $diffInMonths;
//

        $data['purchase_frequency'] = $data['orders_count'] / $data['lifetime'];
        $data['ait'] = 1 / $data['purchase_frequency'];

        $total = Estimate::where([
            'client_id' => $client->id,
            'is_saled' => true
        ])
            ->sum('total');
        $data['customer_equity'] = $total + $estimate->total;

        $data['average_order_value'] = $data['customer_equity'] / $data['orders_count'];
        $data['customer_value'] = $data['average_order_value'] * $data['purchase_frequency'];

        // TODO - 22.04.20 - Lifetime перевести в месяца
        $data['ltv'] = $data['lifetime'] * $data['average_order_value'] * $data['purchase_frequency'];

        // TODO - 22.04.20 - Пока нет промоакций
        $data['use_promo_count'] = 0;
        $data['promo_rate'] = $data['use_promo_count'] / $data['orders_count'];

//        dd($data);

        $client->update($data);

        return $client;
    }

}
