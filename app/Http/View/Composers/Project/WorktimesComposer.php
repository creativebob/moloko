<?php
	
	namespace App\Http\View\Composers\Project;
	use Illuminate\View\View;

// Куки
	use Illuminate\Support\Facades\Cookie;
	
	class WorktimesComposer
	{
		public function compose(View $view)
		{
			
			$site = $view->site->load(['filials' => function ($q) {
				$q->with([
					'location.city',
					'schedules.worktimes'
				])
					->where('display', true);
//        	->whereHas('location', function ($q) {
//        		$q->where('city_id', Cookie::get('city_id'));
//        	});
			}]);

//         dd($site);
			
			// TODO - 22.01.19 - Решили пока вытаскивать первый филиал, при развитии будем углубляться и перерабатывать
			$filial = $site->filials->first();
//         dd($filial);
			
			$worktimes = [];
			foreach ($filial->schedules[0]->worktimes as $worktime) {
				// dd($worktime);
				$worktimes[$worktime->weekday]['worktime_begin'] = secToTime($worktime->worktime_begin);
				$worktimes[$worktime->weekday]['worktime_end'] = secToTime($worktime->worktime_begin + $worktime->worktime_interval);
			}
//         dd($worktimes);
			
			$days = [
				'1' => 'Понедельник',
				'2' => 'Вторник',
				'3' => 'Среда',
				'4' => 'Четверг',
				'5' => 'Пятница',
				'6' => 'Суббота',
				'7' => 'Воскресенье',
			];
//        dd($days);
			
			return $view->with(compact('worktimes', 'days'));
		}
		
	}
