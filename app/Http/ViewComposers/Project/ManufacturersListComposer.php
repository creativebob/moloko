<?php

namespace App\Http\ViewComposers\Project;

use Illuminate\View\View;

class ManufacturersListComposer
{
	public function compose(View $view)
	{

        $company = $view->site->company->load('manufacturers.attachments.article');
        $manufacturers_list = $company->manufacturers->where('display', true);

        return $view->with('manufacturers_list', $manufacturers_list);
    }
}