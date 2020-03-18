<?php

namespace App\Http\View\Composers\Project;
use Illuminate\View\View;

class WorktimeComposer
{
    public function compose(View $view)
    {
        $site = $view->site;
        $worktimes = [];

        if (isset($site->company->worktime)) {
            $worktimes = $view->site->company->worktime;
        }
        if (isset($site->filial->worktime)) {
            $worktimes = $view->site->filial->worktime;
        }

        return $view->with(compact('worktimes'));
    }
}
