<?php

namespace App\Http\ViewComposers;

use App\Country;

use Illuminate\View\View;

class CountriesComposer
{
	public function compose(View $view)
	{

		$countries_list = Country::get()->pluck('name', 'id');
		return $view->with('countries_list', $countries_list);

	}

}