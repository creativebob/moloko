<?php

namespace App\Console\Commands;

use App\Client;
use App\ClientsIndicator;
use App\Company;
use App\Estimate;
use App\Unit;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ClientsIndicatorsReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clients-indicators:report
                            {startDate? : Дата начала отчёта}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Расчёт показателей общей клиентской базы за период';

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
     */
    public function handle()
    {
        set_time_limit(0);

        // Месяц
        $unit = Unit::findOrFail(17);

        $date = $this->argument('startDate');
        if (! $date) {
            // Если крон
            $date = today()->subMonth()->toDateString();
        }

        $startDate = Carbon::create($date);

        // TODO - 29.04.20 - Добавить алиасы к units
        $endDate = Carbon::create($date)->addMonth();

        $startDatePeriodActive = Carbon::create($date)->subYear();
        $endDatePeriodActive = Carbon::create($date)->addMonth()->subYear();

        $daysInPeriod = $startDate->diffInDays($endDate);
//        dd($startDate, $endDate, $unit, $startDatePeriodActive, $endDatePeriodActive);

        $groupedClients = Client::where('first_order_date', '<', $endDate)
            ->where('orders_count', '>', 0)
            ->has('estimates')
            ->with([
                'estimates' => function ($q) use ($endDate) {
                    $q->where('registered_date', '<', $endDate);
                },
                'actual_blacklist',
                'loyality_score'
            ])
            ->withCount([
                'estimates' => function ($q) use ($endDate) {
                    $q->where('registered_date', '<', $endDate);
                }
            ])
//            ->whereHas('estimates', function($q)  use ($endDate) {
//                $q->where('registered_date', '<', $endDate);
//            })
            //            ->when($authUser, function ($q) use ($authUser) {
            //                $q->where('company_id', $authUser->company_id);
            //            })
            ->get()
            ->groupBy('company_id');
//        dd($groupedClients);

        $data = [];
        $data['start_date'] = $startDate;
        $data['unit_id'] = 17;
        $data['author_id'] = 1;

        $clientsIndicators = [];
        foreach ($groupedClients as $companyId => $clients) {
            $data['company_id'] = $companyId;

            $company = Company::findOrFail($companyId);

            $clientsIndicator = ClientsIndicator::where([
                'start_date' => $data['start_date'],
                'unit_id' => $data['unit_id'],
                'company_id' => $data['company_id']
            ])
                ->first();

            if ($clientsIndicator) {
                logs('clients')->info("Отчет по показателям от {$startDate->format('d.m.Y')} на период: {$unit->name} для компании {$company->name} уже существует.");
                $clientsIndicators[] = $clientsIndicator;
                //                return $clientsIndicator;
                //                abort(403, "Отчет за {$clientsIndicator->start_date->format('d.m.Y')} уже существует в бд.");
            } else {
                $data['count'] = $clients->count();

//                dd($clients->first()->estimates->last());
                $activeClients = $clients->filter(function ($client) use ($endDatePeriodActive) {
                    return $client->estimates->last()->registered_date >= $endDatePeriodActive;
                });
                $data['active_count'] = $activeClients->count();

                $activeClientsPrevious = $clients->filter(function ($client) use ($startDate, $startDatePeriodActive) {
                    return $client->first_order_date < $startDate && $client->estimates->last()->registered_date >= $startDatePeriodActive;
                });
                $data['active_previous_count'] = $activeClientsPrevious->count();

                $lostClients = $clients->filter(function ($client) use ($startDatePeriodActive, $endDatePeriodActive) {
                    return $client->estimates->last()->registered_date < $endDatePeriodActive;
                });
                $data['lost_count'] = $lostClients->count();

                $data['deleted_count'] = Client::whereNotNull('deleted_at')->withTrashed()->count();
                $data['blacklist_count'] = $clients->whereNotNull('actual_blacklist')->count();

                $data['new_clients_period_count'] = $clients->where('first_order_date', '>=', $startDate)->where('first_order_date', '<', $endDate)->count();

                $lostClientsPeriod = $clients->filter(function ($client) use ($startDatePeriodActive, $endDatePeriodActive) {
                    return $client->estimates->last()->registered_date >= $startDatePeriodActive && $client->estimates->last()->registered_date < $endDatePeriodActive;
                });
                $data['lost_clients_period_count'] = $lostClientsPeriod->count();

                if (($data['active_previous_count'] + $data['new_clients_period_count']) != $data['active_count']) {
                    logs('clients')->info("Отчет по показателям от {$startDate->format('d.m.Y')} на период: {$unit->name} для компании {$company->name}: ОБНАРУЖЕНО НЕСООТВЕТСТВИЕ КЛИЕНТОВ!");
                }



                $data['customer_retention_rate'] = ($data['active_count'] - $data['new_clients_period_count']) / $data['active_previous_count'];
                $data['churn_rate'] = $data['lost_clients_period_count'] / $data['active_previous_count'];

                $customersPeriod = $clients->filter(function ($client) use ($startDate, $endDate) {
                    return $client->estimates->last()->registered_date >= $startDate && $client->estimates->last()->registered_date < $endDate;
                });
                $data['customers_period_count'] = $customersPeriod->count();

                $estimates = Estimate::where([
                    'company_id' => $companyId,
                    'is_registered' => true
                ])
                    ->where('registered_date', '<', $endDate)
                    ->get();

//                dd($startDate);
                $registeredEstimatesPeriod = $estimates->where('registered_date', '>=', $startDate)
                    ->where('registered_date', '<', $endDate);

                $activeClientsIds = $activeClients->pluck(['id']);
                $activeClientsRegisteredEstimates = $estimates->whereIn('client_id', $activeClientsIds);

                $data['orders_count'] = $activeClientsRegisteredEstimates->count();
                $data['orders_period_count'] = $registeredEstimatesPeriod->count();
                $data['lead_close_rate'] = $data['orders_period_count'] / $estimates->where('registered_date', '>=', $startDate)->count();

                $repeatClientsCount = 0;
                foreach ($clients as $client) {
                    $estimatesPeriod = $client->estimates->where('is_registered', true)->where('registered_date', '>=', $startDate)->where('registered_date', '<', $endDate);

                    if ($estimatesPeriod->count() > 1) {
                        $repeatClientsCount++;
                    }
                }
                $data['repeat_purchase_rate'] = $repeatClientsCount / $data['customers_period_count'];

                $sumOrdersActiveClients = $activeClients->sum('estimates_count');
                $data['purchase_frequency'] = $sumOrdersActiveClients / $data['active_count'];
                $data['purchase_frequency_period'] = $data['orders_period_count'] / $data['customers_period_count'];
                $data['order_gap_analysis'] = $daysInPeriod / $data['purchase_frequency_period'];

                $data['orders_revenue'] = $activeClientsRegisteredEstimates->sum('total');
                $data['orders_revenue_period'] = $registeredEstimatesPeriod->sum('total');
                $data['arpu'] = $data['orders_revenue_period'] / $data['active_count'];
                $data['arppu'] = $data['orders_revenue_period'] / $data['customers_period_count'];
                $data['paying_share'] = $data['arpu'] / $data['arppu'];

                if ($data['customer_retention_rate'] < 1) {
                    $data['lifetime'] = 100 / (100 - ($data['customer_retention_rate'] * 100));
                } else {
                    $data['lifetime'] = 12;
                }

                // TODO - 27.04.20 - Если нет потерянных клиентов, то деление на 0
//                if ($data['lost_count'] > 0) {
//                    $data['lifetime_fact'] = $lostClients->sum('lifetime') / $data['lost_count'];
//                } else {
//                    $data['lifetime_fact'] = 0;
//                }

                $data['average_order_value'] = $data['orders_revenue'] / $data['orders_count'];
                $data['average_order_value_period'] = $data['orders_revenue_period'] / $data['orders_period_count'];

                $data['customer_value'] = $data['average_order_value'] * $data['purchase_frequency'];
                $data['customer_value_period'] = $data['average_order_value_period'] * $data['purchase_frequency_period'];

                $data['ltv'] = $data['lifetime'] * $data['average_order_value'] * $data['purchase_frequency'];
                $data['ltv_period'] = $data['lifetime'] * $data['average_order_value_period'] * $data['purchase_frequency_period'];
                $data['customer_equity'] = $data['ltv'] * $data['active_count'];

                $loyalityClients = $activeClients->whereNotNull('loyality_score');

                if ($loyalityClients->isNotEmpty()) {
                    $loyalityClientsCount = $loyalityClients->count();
                    $promotersCount = 0;
                    $detractorsCount = 0;

                    foreach ($loyalityClients as $loyalityClient) {
                        if ($loyalityClient->loyality_score->loyality_score < 7) {
                            $detractorsCount++;
                        }
                        if ($loyalityClient->loyality_score->loyality_score > 8) {
                            $promotersCount++;
                        }
                    }

                    $detractorsPercent = ($detractorsCount * 100) / $loyalityClientsCount;
                    $promotersPercent = ($promotersCount * 100) / $loyalityClientsCount;

                    $data['nps'] = $promotersPercent - $detractorsPercent;
                } else {
                    $data['nps'] = 0;
                }


//                dd($data);
                $clientsIndicator = ClientsIndicator::create($data);

                logs('clients')->info("Сформирован отчет по показателям клиентской базы от {$startDate->format('d.m.Y')} на период: {$unit->name}, для компании: {$company->name}");
                $clientsIndicators[] = $clientsIndicator;
                //                return $clientsIndicator;
            }


//        $res = \Artisan::call('clients-indicators:report');
//        dd($res);
        }

//        dd($clientsIndicators);
    }
}
