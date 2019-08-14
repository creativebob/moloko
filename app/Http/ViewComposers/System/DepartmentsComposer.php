<?php

namespace App\Http\ViewComposers\System;

use App\Department;

use Illuminate\View\View;

class DepartmentsComposer
{
	public function compose(View $view)
	{

        $answer = operator_right('departments', true, 'index');

        if (isset($view->filials)) {
            $departments = Department::moderatorLimit($answer)
            ->companiesLimit($answer)
            ->whereNull('parent_id')
            ->get(['id', 'name']);
        }

        return $view->with('departments', $departments);

    }

}