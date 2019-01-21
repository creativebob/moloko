<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;

class FilialsForUserComposer
{
	public function compose(View $view)
	{

		$filial_list = getLS('users', 'view', 'filials');
		return $view->with('filial_list', $filial_list);

	}

}