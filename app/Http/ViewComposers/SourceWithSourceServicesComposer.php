<?php

namespace App\Http\ViewComposers;

use App\Source;
use App\SourceService;

use Illuminate\View\View;

class SourceWithSourceServicesComposer
{
	public function compose(View $view)
	{

		// dd(isset($view->source_service));
        // Главный запрос
        $sources = Source::get();

        $source_id = isset($view->source_service) ? $view->source_service->source_id : $sources->first()->id;

        $source_services = SourceService::where('source_id', $source_id)->get();

        return $view->with(compact('sources', 'source_services'));
    }
}