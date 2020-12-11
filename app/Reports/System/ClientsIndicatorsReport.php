<?php

namespace App\Reports\System;

use App\Client;
use App\ClientsIndicator;
use App\Company;
use App\Models\System\Documents\Estimate;
use App\Unit;
use Carbon\Carbon;

class ClientsIndicatorsReport
{
    /**
     * Получение показателей клиентской базы
     *
     * @param null $date
     * @param string $period
     * @param null $companyId
     * @return mixed
     */
    public static function getIndicators($date = null, $period = 'month', $companyId = null)
    {
        set_time_limit(0);

        // Если крон
        if (!$date) {
            $date = today()->subMonth()->toDateString();
        }

        switch ($period) {
            case 'month':
                // Месяц
                // TODO - 29.04.20 - Добавить алиасы к units
                $unit = Unit::find(17);

                $startDate = Carbon::create($date);
                $endDate = Carbon::create($date)->addMonth();

                $startDatePeriodActive = Carbon::create($date)->subYear();
                $endDatePeriodActive = Carbon::create($date)->addMonth()->subYear();

                $startDatePreviousPeriodActive = Carbon::create($date)->subYear()->subMonth();
                $endDatePreviousPeriodActive = $startDatePeriodActive;

                $daysInPeriod = $startDate->diffInDays($endDate);
                break;

            case 'year':
                // Год
                // TODO - 29.04.20 - Добавить алиасы к units
                $unit = Unit::find(20);

                $startDate = Carbon::create($date);
                $endDate = Carbon::create($date)->addYear();

                $startDatePeriodActive = Carbon::create($date)->subYear();
                $endDatePeriodActive = Carbon::create($date);

//                $startDatePreviousPeriodActive = Carbon::create($date)->subYear()->subMonth();
//                $endDatePreviousPeriodActive = $startDatePeriodActive;

                $daysInPeriod = $startDate->diffInDays($endDate);
                break;
        }

//        dd($startDate, $endDate, $unit, $startDatePeriodActive, $endDatePeriodActive);

        $groupedClients = Client::where('orders_count', '>', 0)
            ->whereNotNull('first_order_date')
            ->where('first_order_date', '<', $endDate)
//            ->has('estimates')
            ->with([
                'estimates' => function ($q) use ($endDate) {
                    $q->whereNotNull('conducted_at')
                        ->where('conducted_at', '<', $endDate)
                        ->orderBy('created_at')
//                        ->select([
//                            'id',
//                            'client_id',
//                            'conducted_at',
//                            'created_at'
//                        ])
                    ;
                },
                'actual_blacklist:id,client_id',
                'loyalty_score:id,client_id,loyalty_score'
            ])
            ->withCount([
                'estimates' => function ($q) use ($endDate) {
                    $q->whereNotNull('conducted_at')
                        ->where('conducted_at', '<', $endDate);
                }
            ])
//            ->whereHas('estimates', function($q)  use ($endDate) {
//                $q->where('conducted_at', '<', $endDate);
//            })
            //            ->when($authUser, function ($q) use ($authUser) {
            //                $q->where('company_id', $authUser->company_id);
            //            })
            ->when($companyId, function ($q) use ($companyId) {
                $q->where('company_id', $companyId);
            })
            ->get()
            ->groupBy('company_id');
//        dd($groupedClients);

        $data = [];
        $data['start_date'] = $startDate;
        $data['unit_id'] = $unit->id;
        $data['author_id'] = 1;

        $clientsIndicators = [];
        foreach ($groupedClients as $companyId => $clients) {
            $data['company_id'] = $companyId;

            $company = Company::find($companyId);

            $clientsIndicator = ClientsIndicator::firstOrNew([
                'start_date' => $data['start_date'],
                'unit_id' => $data['unit_id'],
                'company_id' => $data['company_id']
            ]);

//            if ($clientsIndicator) {
//                logs('clients')->info("Отчет по показателям от {$startDate->format('d.m.Y')} на период: {$unit->name} для компании {$company->name} уже существует.");
//                $clientsIndicators[] = $clientsIndicator;
//                //                return $clientsIndicator;
//                //                abort(403, "Отчет за {$clientsIndicator->start_date->format('d.m.Y')} уже существует в бд.");
//            } else {
            $data['count'] = $clients->count();

//                dd($clients->first()->estimates->last());
            $activeClients = $clients->filter(function ($client) use ($endDatePeriodActive) {
                if ($client->estimates->last() != null) {
                    return $client->estimates->last()->conducted_at > $endDatePeriodActive;
                }
            });
            $data['active_count'] = $activeClients->count();

            // Находим активных клиентов в предыдущем периоде
            $activeClientsPrevious = $clients->filter(function ($client) use ($startDate, $endDatePreviousPeriodActive) {
                if ($client->estimates->where('conducted_at', '<', $startDate)->last() != null) {
                    return $client->first_order_date < $startDate && $client->estimates->where('conducted_at', '<', $startDate)->last()->conducted_at > $endDatePreviousPeriodActive;
                }
            });
            $data['active_previous_count'] = $activeClientsPrevious->count();
            logs('clients')->info("Количество клиентов которые были действующими в предыдущий период (с {$startDatePreviousPeriodActive} по {$endDatePreviousPeriodActive}): {$activeClientsPrevious->count()}");

            $lostClients = $clients->filter(function ($client) use ($startDatePeriodActive, $endDatePeriodActive) {
                if ($client->estimates->last() != null) {
                    return $client->estimates->last()->conducted_at <= $endDatePeriodActive;
                }
            });
            $data['lost_count'] = $lostClients->count();

            $data['deleted_count'] = Client::whereNotNull('deleted_at')
                ->where('company_id', $companyId)
                ->withTrashed()
                ->count();
            $data['blacklist_count'] = $clients->whereNotNull('actual_blacklist')->count();

            $data['new_clients_period_count'] = $clients->where('first_order_date', '>=', $startDate)->where('first_order_date', '<', $endDate)->count();

            $lostClientsPeriod = $clients->filter(function ($client) use ($startDatePeriodActive, $endDatePeriodActive) {
                if ($client->estimates->last() != null) {
                    return $client->estimates->last()->conducted_at >= $startDatePeriodActive && $client->estimates->last()->conducted_at < $endDatePeriodActive;
                }
            });
            $data['lost_clients_period_count'] = $lostClientsPeriod->count();

            if (($data['active_previous_count'] + $data['new_clients_period_count'] - $data['lost_clients_period_count']) != $data['active_count']) {
                logs('clients')->info("Отчет по показателям от {$startDate->format('d.m.Y')} на период: {$unit->name} для компании {$company->name}: ОБНАРУЖЕНО НЕСООТВЕТСТВИЕ КЛИЕНТОВ!
                        Активные на предыдущий период: [{$data['active_previous_count']}], новые: [{$data['new_clients_period_count']}], потерянные: [{$data['lost_clients_period_count']}], активные на конец: [{$data['active_count']}]
                    ");
            }


            if ($data['active_previous_count'] > 0) {
                $data['customer_retention_rate'] = ($data['active_count'] - $data['new_clients_period_count']) / $data['active_previous_count'];
                $data['churn_rate'] = $data['lost_clients_period_count'] / $data['active_previous_count'];
            } else {
                $data['customer_retention_rate'] = 1;
                $data['churn_rate'] = 0;
            }

            $customersPeriod = $clients->filter(function ($client) use ($startDate, $endDate) {
                if ($client->estimates->last() != null) {
                    return $client->estimates->last()->conducted_at >= $startDate && $client->estimates->last()->conducted_at < $endDate;
                }
            });
            $data['customers_period_count'] = $customersPeriod->count();

            $estimates = Estimate::where([
                'company_id' => $companyId,
            ])
                ->whereNotNull('conducted_at')
                ->where('conducted_at', '<', $endDate)
                ->get();

//                dd($startDate);
            $registeredEstimatesPeriod = $estimates->where('conducted_at', '>=', $startDate)
                ->where('conducted_at', '<', $endDate);

            $activeClientsIds = $activeClients->pluck(['id']);
            $activeClientsRegisteredEstimates = $estimates->whereIn('client_id', $activeClientsIds);

            $data['orders_count'] = $activeClientsRegisteredEstimates->count();
            $data['orders_period_count'] = $registeredEstimatesPeriod->count();
            $data['lead_close_rate'] = $data['orders_period_count'] / $estimates->where('conducted_at', '>=', $startDate)->count();

            $repeatClientsCount = 0;
            foreach ($clients as $client) {
                $estimatesPeriod = $client->estimates->where('conducted_at', '!=', null)->where('conducted_at', '>=', $startDate)->where('conducted_at', '<', $endDate);

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

            $loyalityClients = $activeClients->whereNotNull('loyalty_score');

            if ($loyalityClients->isNotEmpty()) {
                $loyalityClientsCount = $loyalityClients->count();
                $promotersCount = 0;
                $detractorsCount = 0;

                foreach ($loyalityClients as $loyalityClient) {
                    if ($loyalityClient->loyalty_score->loyalty_score < 7) {
                        $detractorsCount++;
                    }
                    if ($loyalityClient->loyalty_score->loyalty_score > 8) {
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
            if ($clientsIndicator->id) {
                $clientsIndicator->update($data);
            } else {
                $clientsIndicator = ClientsIndicator::create($data);
            }
            logs('clients')
                ->info("Сформирован отчет по показателям клиентской базы от {$startDate->format('d.m.Y')} на период: {$unit->name}, для компании: {$company->name}");
//            }
        }

        $result['success'] = true;
        return $result;
    }
}
