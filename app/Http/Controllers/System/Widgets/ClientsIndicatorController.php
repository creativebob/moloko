<?php

namespace App\Http\Controllers\System\Widgets;

use App\ClientsIndicator;
use App\Http\Controllers\Controller;
use App\Reports\System\ClientsIndicatorsReport;
use Illuminate\Http\Request;

class ClientsIndicatorController extends Controller
{

    /**
     * Рассчет клиентских показателей за месяц определенного года
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function computeIndicatorsForMonth(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $res = ClientsIndicatorsReport::getIndicators("{$request->year}-{$request->month}-01", 'month', $companyId);

        if ($res['success']) {
            $clientsIndicatorsForMonth = ClientsIndicator::whereYear('start_date', $request->year)
                ->whereMonth('start_date', $request->month)
                ->where('company_id', $companyId)
                ->where('unit_id', 17)
                ->first();

            return response()->json($clientsIndicatorsForMonth);
        }
    }

    /**
     * Рассчет клиентских показателей за определенный год
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function computeIndicatorsForYear(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $res = ClientsIndicatorsReport::getIndicators("{$request->year}-01-01", 'year', $companyId);

        if ($res['success']) {
            $clientsIndicatorsForYear = ClientsIndicator::whereYear('start_date', $request->year)
                ->where('company_id', $companyId)
                ->where('unit_id', 20)
                ->first();

            return response()->json($clientsIndicatorsForYear);
        }
    }
}
