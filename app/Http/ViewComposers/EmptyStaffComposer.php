<?php

namespace App\Http\ViewComposers;

use App\Staffer;

use Illuminate\View\View;

class EmptyStaffComposer
{
	public function compose(View $view)
	{

        // Список пользователей
        $answer = operator_right('staff', true, 'index');

            $staff = Staffer::with('position', 'department', 'filial')
            ->moderatorLimit($answer)
            ->companiesLimit($answer)
            ->filials($answer)
            ->authors($answer)
            ->systemItem($answer)
            ->whereNull('user_id')
            // ->orderBy('second_name')
            ->get();


        return $view->with('staff', $staff);
    }
}