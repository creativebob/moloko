<?php

namespace App\Http\View\Composers\Project;

use App\Service;
use Illuminate\View\View;

class ServicesComposer
{
	public function compose(View $view)
	{
        $services = Service::with([
            'process.photo',
            'prices',
            'actualFlows'
        ])
        ->where([
            'display' => true,
            'archive' => false
        ])
            ->whereHas('process', function ($q) {
                $q->where('draft', false);
            })
            ->has('actualFlows')
            ->get();
       // dd($services);

        return $view->with(compact('services'));
    }

}
