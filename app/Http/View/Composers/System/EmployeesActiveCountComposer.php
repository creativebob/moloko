<?php

namespace App\Http\View\Composers\System;

use App\Employee;
use Illuminate\View\View;

class EmployeesActiveCountComposer
{
	public function compose(View $view)
	{

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('employees', false, 'index');

        $employees_active_count = Employee::moderatorLimit($answer)
            ->companiesLimit($answer)
            ->authors($answer)
            ->systemItem($answer)
            ->whereNull('dismissal_date')
            ->count();
//        dd($employees_active_count);

        return $view->with(compact('employees_active_count'));
    }

}
