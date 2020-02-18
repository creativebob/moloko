<?php

namespace App\Http\View\Composers\System;

use App\SourceService;

use Illuminate\View\View;

class SourceServicesComposer
{
	public function compose(View $view)
	{


        // Главный запрос
        $source_services = SourceService::where('source_id', $view->source_id)
        ->get();


        return $view->with(compact('source_services'));
    }
}
