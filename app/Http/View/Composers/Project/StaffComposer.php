<?php

namespace App\Http\View\Composers\Project;

use App\Staffer;
use Illuminate\View\View;

class StaffComposer
{
	public function compose(View $view)
	{

        $staff = Staffer::with([
            'user.photo',
            'position:id,name'
        ])
        ->where([
            'display' => true,
            'filial_id' => $view->site->filial->id
        ])
            ->whereNotNull('user_id')
            ->orderBy('sort')
            ->get();
       // dd($staff);

        return $view->with(compact('staff'));
    }

}
