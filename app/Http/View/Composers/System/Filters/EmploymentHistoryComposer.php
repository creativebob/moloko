<?php

namespace App\Http\View\Composers\System\Filters;

use App\Employee;
use App\Staffer;
use Illuminate\View\View;

class EmploymentHistoryComposer
{

    /**
     * Отдаем историю трудоустройства пользователя на шаблон
     *
     * @param View $view
     * @return View
     */
    public function compose(View $view)
    {

        $employee = $view->employee;

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('employees', true, getmethod('edit'));

        $employmentHistory = Employee::with([
            'user',
            'staffer' => function ($q) {
                $q->with([
                    'position',
                    'department',
                    'filial',

                ]);
            }
        ])
            ->moderatorLimit($answer)
            ->where('user_id', $employee->user_id)
            ->get();

        return $view->with(compact('employmentHistory'));
    }
}
