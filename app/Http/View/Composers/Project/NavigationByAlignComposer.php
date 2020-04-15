<?php

namespace App\Http\View\Composers\Project;

use Illuminate\View\View;

class NavigationByAlignComposer
{
    public function compose(View $view)
    {
        $align = $view->align ?? null;
        $filial_id = $view->site->filial->id;

        $navigation = $view->site->navigations->where('align.tag', $align)->first()->load([
            'menus' => function ($q) use ($filial_id) {
                $q->with([
                    'page'
                ])
                    ->whereNull('filial_id')
                    ->orWhere('filial_id', $filial_id)
                    ->where('display', true)
                    ->orderBy('sort');
            }
        ]);

        return $view->with(compact('navigation'));
    }

}
