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

        $res = ClientsIndicatorsReport::getIndicators("{$request->year}-{$request->month}-01", $companyId);

        if ($res['success']) {
            $clientsIndicatorsForMonth = ClientsIndicator::whereYear('start_date', $request->year)
                ->whereMonth('start_date', $request->month)
                ->where('company_id', $companyId)
                ->first();

            return response()->json($clientsIndicatorsForMonth);
        }
    }
//
//    public function getIndicatorsForYear(Request $request)
//    {
//        $companyId = auth()->user()->company_id;
//
//        $clientsIndicatorsForYear = ClientsIndicator::whereYear('start_date', $request->year)
//            ->where('company_id', $companyId)
//            ->orderBy('start_date')
//            ->get();
//
//        $data = [];
//        foreach($clientsIndicatorsForYear as $clientsIndicatorMonth) {
//            $result['data'][$request->year][$clientsIndicatorMonth->start_date->format('n')] = $clientsIndicatorMonth;
//        }
////        dd($data);
//
//        return response()->json($data);
//    }
}
