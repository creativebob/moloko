<?php

namespace App\Http\ViewComposers\Project;

use Illuminate\View\View;

// Куки
use Illuminate\Support\Facades\Cookie;

class DepartmentsComposer
{
	public function compose(View $view)
	{
//        dd($view->site);
//        $site = $view->site->load(['filials' => function ($q) {
//        	$q->with('location.city')
//        	->where('display', true)
//        	->whereHas('location', function ($q) {
//        		$q->where('city_id', Cookie::get('city_id'));
//        	});
//        }]);


        // TODO 22.01.19 - Решили пока вытаскивать первый филиал, при развитии будем углубляться и перерабатывать
        $filial = $view->site->filials->first();
//         dd($filial);

        return $view->with('filial', $filial);
    }

}