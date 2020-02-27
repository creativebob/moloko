<?php

namespace App\Http\View\Composers\Project;

use Illuminate\View\View;

class VendorsComposer
{
	public function compose(View $view)
	{
        $company = $view->site->company->load(['vendors' => function ($q) {
            $q->with([
                'supplier.company.photo'
            ])
                ->where('display', true)
                ->where('archive', false);
        }]);

        return $view->with('vendors', $company->vendors);
    }
}
