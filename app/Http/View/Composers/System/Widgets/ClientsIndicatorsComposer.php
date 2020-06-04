<?php

namespace App\Http\View\Composers\System\Widgets;

use App\ClientsIndicator;
use Illuminate\View\View;

class ClientsIndicatorsComposer
{
	public function compose(View $view)
	{
        $curYear = (int) today()->format('Y');

        $groupedClientsIndicatorsCurYear = ClientsIndicator::where('company_id', auth()->user()->company_id)
            ->orderBy('start_date')
            ->get()
            ->groupBy(function ($item) {
                return $item->start_date->format('Y');
            });

//        dd($groupedClientsIndicatorsCurYear);
        $data['data'][$curYear] = [];
        foreach($groupedClientsIndicatorsCurYear as $year => $clientsIndicators) {
            foreach($clientsIndicators as $clientsIndicator) {
                if ($clientsIndicator->unit_id == 17) {
                    $data['data'][$year]['months'][$clientsIndicator->start_date->format('n')] = $clientsIndicator;
                }
                if ($clientsIndicator->unit_id == 20) {
                    $data['data'][$year]['year'] = $clientsIndicator;
                }

            }
        }
//        dd($data);

        if (isset(auth()->user()->company->foundation_date)) {
            $startYear = (int) auth()->user()->company->foundation_date->format('Y');

//            dd($startYear, $curYear);

            $yearsList = [];
            for ($i = $startYear; $i <= $curYear; $i++) {
                $yearsList[] = $i;
            }
//            dd($yearsList);
        } else {
            $yearsList[] = $curYear;
        }

        $data['yearsList'] = $yearsList;

        return $view->with(compact('data'));
	}
}
