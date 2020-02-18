<?php

namespace App\Http\View\Composers\Project;
use Illuminate\View\View;

class WorktimeFilialTodayComposer
{
    public function compose(View $view)
    {
        $worktimes = $view->site->filial->worktime;
        if ($worktimes[date('N')]['begin'] && $worktimes[date('N')]['begin']) {
            $worktime = $worktimes[date('N')]['begin'] . ' - ' . $worktimes[date('N')]['end'];
        } else {
            $worktime = 'Выходной';
        }
        return $view->with(compact('worktime'));

//        $worktimes = $view->site->filial->worktime;
//        return $view->with(compact('worktimes'));
    }

}
