<?php

namespace App\Http\View\Composers\Project;

use Illuminate\View\View;

class NavigationByAlignComposer
{
    public function compose(View $view)
    {
        $align = $view->align ?? null;
        $filial_id = $view->site->filial->id;

        $navigation = $view->site->navigations->where('align.tag', $align)->first();

        return $view->with(compact('navigation'));
    }

}
