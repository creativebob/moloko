<?php

namespace App\Console\Commands\System;

use App\Client;
use App\Estimate;
use Illuminate\Console\Command;

class ClientsIndicatorsDay extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clients-indicators:day';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Расчёт показателей клиента каждый день';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        set_time_limit(0);

        $groupedClients = Client::with([
            'estimates'
        ])
            ->where('orders_count', '>', 0)
            ->get()
            ->groupBy('company_id');

        foreach ($groupedClients as $company_id => $clients) {

            $estimatesTotalSum = Estimate::where([
                'company_id' => $company_id,
                'is_registered' => true
            ])
                ->whereDate('registered_date', '>', today()->subYear())
                ->sum('total');
//            dd($estimatesTotalSum);

            $data = [];

            $total80Percent = ($estimatesTotalSum / 100) * 80;
            $total95Percent = ($estimatesTotalSum / 100) * 95;
            $limit80Percent = 0;
            $limit95Percent = 0;
            $continue80Percent = true;
            $continue95Percent = true;

            $clients = $clients->sortByDesc(function ($client) {
                return $client->estimates->where('registered_date', '>=', today()->subYear())->sum('total');
            });

            $clientsA = [];
            $clientsB = [];

            $i = 0;
            $len = $clients->count();

            foreach ($clients as $client) {
                $data['is_lost'] = ($client->last_order_date <= today()->subYear());

                // TODO - 18.09.20 - Изменения в частоте
                $factLifetime = $client->first_order_date->diffInMonths(today());
                if ($factLifetime == 0) {
                    $factLifetime = 1;
                }
                $data['purchase_frequency'] = $client->orders_count / $factLifetime;
                $data['customer_value'] = $client->average_order_value * $data['purchase_frequency'];

                $period = 90;

                $firstPeriodStart = today()->subDays($period);
                $secondPeriodStart = today()->subDays($period * 2);
                $thirdPeriodStart = today()->subDays($period * 3);
                $fourthPeriodStart = today()->subDays($period * 4);

                $registeredEstimates = $client->estimates->where('is_registered', true);

                $fourthPeriodOrders = 0;
                $estimateFourthPeriodCount = $registeredEstimates->whereBetween('registered_date', [$fourthPeriodStart, $thirdPeriodStart])->count();
                if ($estimateFourthPeriodCount > 0) {
                    $fourthPeriodOrders = 1;
                }

                $thirdPeriodOrders = 0;
                $estimateThirdPeriodCount = $registeredEstimates->whereBetween('registered_date', [$thirdPeriodStart, $secondPeriodStart])->count();
                if ($estimateThirdPeriodCount > 0) {
                    $thirdPeriodOrders = 1;
                }

                $secondPeriodOrders = 0;
                $estimateSecondPeriodCount = $registeredEstimates->whereBetween('registered_date', [$secondPeriodStart, $firstPeriodStart])->count();
                if ($estimateSecondPeriodCount > 0) {
                    $secondPeriodOrders = 1;
                }

                $firstPeriodOrders = 0;
                $estimateFirstPeriodCount = $registeredEstimates->whereBetween('registered_date', [$firstPeriodStart, today()])->count();
                if ($estimateFirstPeriodCount > 0) {
                    $firstPeriodOrders = 1;
                }

                $activity = $fourthPeriodOrders . $thirdPeriodOrders . $secondPeriodOrders . $firstPeriodOrders;
                $data['activity'] = $activity;

                if ($data['is_lost']) {
                    $data['rfm'] = null;
                    $data['abc'] = null;
                    $data['abcxyz'] = null;
                    $data['is_vip_abc'] = false;
                } else {
                    $recency = today()->diffInDays($client->last_order_date);
                    $r = 0;
                    if ($recency <= 15) {
                        $r = 1;
                    } elseif ($recency > 15 && $recency <= 30) {
                        $r = 2;
                    } elseif ($recency > 30 && $recency <= 60) {
                        $r = 3;
                    } elseif ($recency > 60 && $recency <= 90) {
                        $r = 4;
                    } else {
                        $r = 5;
                    }

                    $frequency = $estimateFirstPeriodCount;
                    $f = 0;
                    if ($frequency >= 6) {
                        $f = 1;
                    } elseif ($frequency >= 3 && $frequency < 6) {
                        $f = 2;
                    } elseif ($frequency == 2) {
                        $f = 3;
                    } elseif ($frequency == 1) {
                        $f = 4;
                    } else {
                        $f = 5;
                    }

                    $monetary = $registeredEstimates->whereBetween('registered_date', [$firstPeriodStart, today()])->sum('total');
                    $m = 0;
                    if ($monetary >= 8000) {
                        $m = 1;
                    } elseif ($monetary >= 4000 && $monetary < 8000) {
                        $m = 2;
                    } elseif ($monetary >= 2000 && $monetary < 4000) {
                        $m = 3;
                    } elseif ($monetary >= 1000 && $monetary < 2000) {
                        $m = 4;
                    } else {
                        $m = 5;
                    }

                    $rfm = $r . $f . $m;
                    $data['rfm'] = (int)$rfm;

                    $clientEstimatesTotalYearSum = $client->estimates
                        ->where('registered_date', '>=', today()->subYear())
                        ->where('is_dismissed', false)
                        ->sum('total');
//                dd($clientEstimatesTotalYearSum);
                    if ($continue80Percent) {
                        $limit80Percent += $clientEstimatesTotalYearSum;
                    }
                    if ($continue95Percent) {
                        $limit95Percent += $clientEstimatesTotalYearSum;
                    }

                    if ($limit80Percent >= $total80Percent) {
                        if ($continue80Percent) {
                            $data['abc'] = 'A';
                            $continue80Percent = false;
                        } else {
                            if ($limit95Percent >= $total95Percent) {
                                if ($continue95Percent) {
                                    $data['abc'] = 'B';
                                    $continue95Percent = false;
                                } else {
                                    $data['abc'] = 'C';
                                }
                            } else {
                                $data['abc'] = 'B';
                            }
                        }
                    } else {
                        $data['abc'] = 'A';
                    }

                    if ($data['abc'] == 'A') {
                        $clientsA[$client->id] = $clientEstimatesTotalYearSum;
                    }
                    if ($data['abc'] == 'B') {
                        $clientsB[$client->id] = $clientEstimatesTotalYearSum;
                    }

                    if (isset($data['abc']) && isset($data['xyz'])) {
                        $data['abcxyz'] = $data['abc'] . $data['xyz'];
                    }

                    $data['is_vip_abc'] = false;
                }

//                dd($data);
                $client->update($data);

                // Последняя итерация
                if ($i == $len - 1) {

                    $clientsBTotalMax = max($clientsB);

                    $vipIds = [];
                    $vipTotal = $clientsBTotalMax * 10;
                    Foreach ($clientsA as $id => $total) {
                        if ($total >= $vipTotal) {
                            $vipIds[] = $id;
                        }
                    }

                    CLient::whereIn('id', $vipIds)->update([
                        'is_vip_abc' => true
                    ]);

                    // TODO - 28.04.20 - Снятие отметки vip

                    unset($clientsA);
                    unset($clientsB);
                }

                $i++;
            }
        }
    }
}
