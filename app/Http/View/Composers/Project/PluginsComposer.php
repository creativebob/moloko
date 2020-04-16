<?php

namespace App\Http\View\Composers\Project;

use Illuminate\View\View;

class PluginsComposer
{

    public function compose(View $view)
    {
        $site = $view->site->domain->load('plugins');
        $plugins = $site->domain->plugins;

        return $view->with(compact('plugins'));
    }

}
