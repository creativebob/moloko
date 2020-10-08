<?php

namespace App\Http\View\Composers\Project;

use Illuminate\View\View;

class ManufacturersListComposer
{
	public function compose(View $view)
	{
        $company = $view->site->company->load(['manufacturers' => function ($q) {
            $q->with([
                'attachments.article', 'company.sites.domains'
            ])
                ->where('display', true);
        }]);

        return $view->with('manufacturers_list', $company->manufacturers);
//        $company = $view->site->company->load('manufacturers.attachments.article');
//        $manufacturers_list = $company->manufacturers->where('display', true);
//
//        return $view->with('manufacturers_list', $manufacturers_list);
    }
}
