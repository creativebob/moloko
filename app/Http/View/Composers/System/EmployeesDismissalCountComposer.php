<?php

namespace App\Http\View\Composers\System;

use App\Employee;
use Illuminate\View\View;

class EmployeesDismissalCountComposer
{
	public function compose(View $view)
	{

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('employees', false, 'index');

        $employees_dismissal_count = Employee::moderatorLimit($answer)
            ->companiesLimit($answer)
            ->authors($answer)
            ->systemItem($answer)
            ->whereNotNull('dismissal_date')
            ->count();
//        dd($employees_dismissal_count);

        return $view->with(compact('employees_dismissal_count'));
    }

}
