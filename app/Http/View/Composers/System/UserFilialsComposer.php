<?php

namespace App\Http\View\Composers\System;

use Illuminate\View\View;

class UserFilialsComposer
{
	public function compose(View $view)
	{
	    $catalog = $view->catalog;
	    $filials = $catalog->filials->pluck('name', 'id')->toArray();

        $userFilials  = session('access.all_rights.index-prices_goods-allow.filials');
//        dd($userFilials);

        $filials = array_intersect_assoc($filials, $userFilials);
//        dd($filials);

		return $view->with(compact('filials'));
	}
}
