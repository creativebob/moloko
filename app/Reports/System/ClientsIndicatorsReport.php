<?php

namespace App\Reports\System;

use App\Client;
use App\ClientsIndicator;
use App\Company;
use App\Estimate;
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
    public static function getIndicators($date = null, $period = 'month', $companyId = null) {
        set_time_limit(0);

        // Если крон
        if (! $date) {
            $date = today()->subMonth()->toDateString();
        }

        switch ($period) {
            case 'month':
                // Месяц
                // TODO - 29.04.20 - Добавить алиасы к units
                $unit = Unit::findOrFail(17);

                $startDate = Carbon::create($date);
                $endDate = Carbon::create($date)->addMonth();

                $startDatePeriodActive = Carbon::create($date)->subYear();
                $endDatePeriodActive = Carbon::create($date)->addMonth()->subYear();

                $daysInPeriod = $startDate->diffInDays($endDate);
                break;

            case 'year':
                // Год
                // TODO - 29.04.20 - Добавить алиасы к units
                $unit = Unit::findOrFail(20);

                $startDate = Carbon::create($date);
                $endDate = Carbon::create($date)->addYear();

                $startDatePeriodActive = Carbon::create($date)->subYear();
                $endDatePeriodActive = Carbon::create($date);

                $daysInPeriod = $startDate->diffInDays($endDate);
                break;
        }

//        dd($startDate, $endDate, $unit, $startDatePeriodActive, $endDatePeriodActive);

        $groupedClients = Client::where('first_order_date', '<', $endDate)
            ->where('orders_count', '>', 0)
            ->has('estimates')
            ->with([
                'estimates' => function ($q) use ($endDate) {
                    $q->where('registered_date', '<', $endDate);
                },
                'actual_blacklist',
                'loyalty_score'
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

            $company = Company::findOrFail($companyId);

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
//                    if ($client->estimates->last() == null) {
//                        dd($client);
//                    }
                    if ($client->estimates->last() != null) {
                        return $client->estimates->last()->registered_date >= $endDatePeriodActive;
                    }
                });
                $data['active_count'] = $activeClients->count();

                $activeClientsPrevious = $clients->filter(function ($client) use ($startDate, $startDatePeriodActive) {
                    if ($client->estimates->last() != null) {
                        return $client->first_order_date < $startDate && $client->estimates->last()->registered_date >= $startDatePeriodActive;
                    }
                });
                $data['active_previous_count'] = $activeClientsPrevious->count();

                $lostClients = $clients->filter(function ($client) use ($startDatePeriodActive, $endDatePeriodActive) {
                    if ($client->estimates->last() != null) {
                        return $client->estimates->last()->registered_date < $endDatePeriodActive;
                    }
                });
                $data['lost_count'] = $lostClients->count();

                $data['deleted_count'] = Client::whereNotNull('deleted_at')->withTrashed()->count();
                $data['blacklist_count'] = $clients->whereNotNull('actual_blacklist')->count();

                $data['new_clients_period_count'] = $clients->where('first_order_date', '>=', $startDate)->where('first_order_date', '<', $endDate)->count();

                $lostClientsPeriod = $clients->filter(function ($client) use ($startDatePeriodActive, $endDatePeriodActive) {
                    if ($client->estimates->last() != null) {
                        return $client->estimates->last()->registered_date >= $startDatePeriodActive && $client->estimates->last()->registered_date < $endDatePeriodActive;
                    }
                });
                $data['lost_clients_period_count'] = $lostClientsPeriod->count();

                if (($data['active_previous_count'] + $data['new_clients_period_count']) != $data['active_count']) {
                    logs('clients')->info("Отчет по показателям от {$startDate->format('d.m.Y')} на период: {$unit->name} для компании {$company->name}: ОБНАРУЖЕНО НЕСООТВЕТСТВИЕ КЛИЕНТОВ!");
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
                        return $client->estimates->last()->registered_date >= $startDate && $client->estimates->last()->registered_date < $endDate;
                    }
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


                logs('clients')->info("Сформирован отчет по показателям клиентской базы от {$startDate->format('d.m.Y')} на период: {$unit->name}, для компании: {$company->name}");
//            }
        }

        $result['success'] = true;
        return $result;
    }
}
