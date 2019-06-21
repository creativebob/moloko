<?php

namespace App\Http\ViewComposers;

use App\Department;

use Illuminate\View\View;

class UserFilialsComposer
{
	public function compose(View $view)
	{
        $user_filials  = session('access.all_rights.index-prices_services-allow.filials');
        // dd($user_filials);

		$filials = Department::
        // whereIn('id', array_keys($user_filials))
        // ->
        whereNull('parent_id')
        ->toBase()
        ->get();

		return $view->with(compact('filials'));

	}

}
