<?php

namespace App\Http\View\Composers\System;

use App\Loyalty;

use Illuminate\View\View;

class LoyaltiesComposer
{
	public function compose(View $view)
	{

        $loyalties_list = Loyalty::get()->pluck('name', 'id');
		return $view->with('loyalties_list', $loyalties_list);

	}

}
