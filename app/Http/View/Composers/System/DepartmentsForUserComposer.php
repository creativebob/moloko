<?php

namespace App\Http\View\Composers\System;

use App\Department;
use Illuminate\View\View;

class DepartmentsForUserComposer
{
	public function compose(View $view)
	{

        $answer = operator_right('departments', false, 'index');

        $departments = Department::moderatorLimit($answer)
            ->companiesLimit($answer)
            ->filials($answer)
            ->authors($answer)
            ->systemItem($answer)
            ->template($answer)
            ->get();

        return $view->with(compact('departments'));

	}

}
