<?php
	
	namespace App\Http\ViewComposers\Project;
	
	use Illuminate\Support\Facades\Cookie;
	use Illuminate\View\View;
	
	class FilialComposer
	{
		
		public function compose(View $view)
		{
			
			$site = $view->site->load('filials');
			
			$filial = $site->filials->first()->load('location.city');
			
			return $view->with(compact('filial'));
		}
		
	}