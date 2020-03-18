<?php

namespace App\Http\View\Composers\Project;
use Illuminate\View\View;

class WorktimeComposer
{
    public function compose(View $view)
    {
        // Смотрим филиал
        $worktimes = [];

        if (isset($view->site->filial->worktime))

        return $view->with(compact('worktime'));

//        $worktimes = $view->site->filial->worktime;
//        return $view->with(compact('worktimes'));
    }

}
