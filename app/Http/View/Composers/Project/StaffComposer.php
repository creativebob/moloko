<?php

namespace App\Http\View\Composers\Project;

use Illuminate\View\View;

class StaffComposer
{
	public function compose(View $view)
	{

       // dd($view->site->load(['company.staff']));
        $site = $view->site->load(['company.staff' => function ($q) {
            $q->with([
                'user.photo',
                'position:id,name'
            ])
            ->where('display', true);
        }]);

        $staff = $site->company->staff;
       // dd($staff);

        return $view->with(compact('staff'));
    }

}
