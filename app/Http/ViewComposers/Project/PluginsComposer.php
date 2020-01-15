<?php

namespace App\Http\ViewComposers\Project;

use Illuminate\View\View;

class PluginsComposer
{

    public function compose(View $view)
    {
        $site = $view->site;
        $site->domains->load('plugins');
        $plugins = $site->domain->plugins;

        return $view->with(compact('plugins'));
    }

}
