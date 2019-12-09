<?php
	
	namespace App\Http\ViewComposers\Project;
	use Illuminate\View\View;
	
	class WorktimeFilialTodayComposer
	{
		public function compose(View $view)
		{
			$filial = $view->site->filial_mode;

			if(!isset($filial)){

				// abort(403, 'В график работ не передан выбранный на сайте филиал');
				$filial = $view->site->filials->first();
			}

			$worktimes = $filial->worktime;
			
			return $view->with(compact('worktimes', 'days'));
		}
		
	}