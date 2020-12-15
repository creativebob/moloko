<?php

namespace App\Http\View\Composers\System;

use Illuminate\View\View;

class AccessFilialsComposer
{
	public function compose(View $view)
	{
        $filials  = session('access.company_info.filials_for_user');
//        dd($filials);

		return $view->with(compact('filials'));
	}
}
