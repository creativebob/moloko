<?php

namespace App\Http\View\Composers\Project;

use Illuminate\View\View;

class ClientsCompaniesListComposer
{
	public function compose(View $view)
	{
        $company = $view->site->company->load('clients_companies', function ($q) {
            $q->with([
                'clientable'
            ])
                ->where('display', true);
        });

        return $view->with('clients_companies_list', $company->clients_companies);

//        $company = $view->site->company->load('clients_companies.clientable');
//        $clients_companies_list = $company->clients_companies->where('display', true);
//
//        return $view->with('clients_companies_list', $clients_companies_list);
    }
}
