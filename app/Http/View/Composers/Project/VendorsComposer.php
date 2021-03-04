<?php

namespace App\Http\View\Composers\Project;

use App\Vendor;
use Illuminate\View\View;

class VendorsComposer
{
	public function compose(View $view)
	{
	    $vendors = Vendor::with([
            'supplier.company.photo',
            'files' => function ($q) {
                $q->where('display', true);
            }
        ])
        ->where([
            'company_id' => $view->site->company_id,
            'display' => true,
            'archive' => false,
        ])
        ->get();

        return $view->with(compact('vendors'));
    }
}
