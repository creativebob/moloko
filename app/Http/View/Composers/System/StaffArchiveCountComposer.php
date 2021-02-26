<?php

namespace App\Http\View\Composers\System;

use App\Staffer;
use Illuminate\View\View;

class StaffArchiveCountComposer
{
    public function compose(View $view)
    {
        $res = strpos(request()->url(), 'archives');
        if (!$res) {
            // Получаем из сессии необходимые данные (Функция находиться в Helpers)
            $answer = operator_right('staff', true, 'index');

            $archivesCount = Staffer::moderatorLimit($answer)
                ->companiesLimit($answer)
                ->authors($answer)
                ->systemItem($answer)
                ->onlyArchived()
                ->count();
//        dd($archivesCount);

            return $view->with(compact('archivesCount'));
        }
    }
}
