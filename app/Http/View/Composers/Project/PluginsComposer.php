<?php

namespace App\Http\View\Composers\Project;

use Illuminate\View\View;

class PluginsComposer
{

    public function compose(View $view)
    {
        $domain = $view->site->domain->load([
            'plugins' => function ($q) {
                $q->where('display', true);
            }
        ]);
        $plugins = $domain->plugins;

        return $view->with(compact('plugins'));
    }

}
