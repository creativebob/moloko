<?php

namespace App\Http\View\Composers\System;

use App\Department;

use Illuminate\View\View;

class FilialsComposer
{
	public function compose(View $view)
	{

        $answer = operator_right('departments', true, 'index');

        $filials = Department::moderatorLimit($answer)
        ->companiesLimit($answer)
        ->whereNull('parent_id')
        ->get([
            'id',
            'name'
        ]);

        return $view->with(compact('filials'));

    }

}
