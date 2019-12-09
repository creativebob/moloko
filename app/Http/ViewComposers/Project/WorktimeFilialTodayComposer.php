<?php

namespace App\Http\ViewComposers\Project;
use Illuminate\View\View;

class WorktimeFilialTodayComposer
{
    public function compose(View $view)
    {
        $worktimes = $view->site->filial->worktime;
        return $view->with(compact('worktimes'));
    }

}
