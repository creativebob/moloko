<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;

class UserFilialsComposer
{
	public function compose(View $view)
	{
        $user_filials  = session('access.all_rights.index-prices_services-allow.filials');

		return $view->with(compact('user_filials'));
	}
}
