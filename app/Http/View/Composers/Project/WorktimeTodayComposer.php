<?php

namespace App\Http\View\Composers\Project;
use Illuminate\View\View;

class WorktimeTodayComposer
{
    public function compose(View $view)
    {
        $site = $view->site;
        $worktimes = [];
        $worktime = 'Выходной';

        if (isset($site->filial->worktime)) {
            $worktimes = $view->site->filial->worktime;
        }

        if ($worktimes[date('N')]['begin'] && $worktimes[date('N')]['end']) {
            $worktime = $worktimes[date('N')]['begin'] . ' - ' . $worktimes[date('N')]['end'];
        } else {
            if (isset($site->company->worktime)) {
                $worktimes = $view->site->company->worktime;

                if ($worktimes[date('N')]['begin'] && $worktimes[date('N')]['end']) {
                    $worktime = $worktimes[date('N')]['begin'] . ' - ' . $worktimes[date('N')]['end'];
                }
            }
        }

        return $view->with(compact('worktime'));
    }
}
