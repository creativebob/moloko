<?php

namespace App\Http\View\Composers\System;

use App\Staffer;
use Illuminate\View\View;

class FilialStaffComposer
{
	public function compose(View $view)
	{
        $filialId = $view->filialId;

        // Список пользователей
        $answer = operator_right('staff', true, 'index');

            $staff = Staffer::moderatorLimit($answer)
            ->companiesLimit($answer)
//            ->filials($answer)
            ->authors($answer)
            ->systemItem($answer)
            ->where('filial_id', $filialId)
            ->get();

        return $view->with(compact('staff'));
    }
}
