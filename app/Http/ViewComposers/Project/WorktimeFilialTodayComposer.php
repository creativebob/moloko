<?php

namespace App\Http\ViewComposers\Project;
use Illuminate\View\View;

class WorktimeFilialTodayComposer
{
    public function compose(View $view)
    {
        $filial = $view->site->filial;

        $worktimes = $filial->worktime;

        return $view->with(compact('worktimes', 'days'));
    }

}
