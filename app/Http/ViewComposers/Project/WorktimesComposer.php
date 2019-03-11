<?php

namespace App\Http\ViewComposers\Project;

use Illuminate\View\View;

// Куки
use Illuminate\Support\Facades\Cookie;

class WorktimesComposer
{
	public function compose(View $view)
	{

        $site = $view->site->load(['company.filials' => function ($q) {
        	$q->with([
                'location.city',
                'schedules.worktimes'
            ])
        	->where('display', 1)
        	->whereHas('location', function ($q) {
        		$q->where('city_id', Cookie::get('city_id'));
        	});
        }]);

        // dd($site);

        // 22.01.19 - Решили пока вытаскивать первый филиал, при развитии будем углубляться и перерабатывать
        $filial = $site->company->filials->first();
        // dd($filial);


        $worktimes = [];
        foreach ($filial->schedules[0]->worktimes as $worktime) {
                // dd($worktime);
            $worktimes[$worktime->weekday]['worktime_begin'] = secToTime($worktime->worktime_begin);
            $worktimes[$worktime->weekday]['worktime_end'] = secToTime($worktime->worktime_begin + $worktime->worktime_interval);
        }
        // dd($worktimes);

        return $view->with('worktimes', $worktimes);
    }

}