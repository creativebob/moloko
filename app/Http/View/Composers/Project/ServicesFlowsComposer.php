<?php

namespace App\Http\View\Composers\Project;

use App\Models\System\Flows\ServicesFlow;
use Illuminate\View\View;

class ServicesFlowsComposer
{
	public function compose(View $view)
	{
        $servicesFlows = ServicesFlow::with([
            'process' => function ($q) {
                $q->with([
                   'process.photo' ,
                   'prices'
                ]);
            },
            'events' => function ($q) {
                $q->with([
                    'process' => function ($q) {
                        $q->with([
                            'process',
                        ]);
                    },
                ]);
            }
        ])
        ->where([
            'display' => true,
            'filial_id' => $view->site->filial->id
        ])
            ->oldest('sort')
            ->get();
       // dd($staff);

        return $view->with(compact('servicesFlows'));
    }

}
