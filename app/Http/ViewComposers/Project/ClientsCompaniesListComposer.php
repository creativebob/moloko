<?php

namespace App\Http\ViewComposers\Project;

use Illuminate\View\View;

class ClientsCompaniesListComposer
{
	public function compose(View $view)
	{

        $company = $view->site->company->load('clients_companies.clientable');
        $clients_companies_list = $company->clients_companies->where('display', true);

        return $view->with('clients_companies_list', $clients_companies_list);
    }
}