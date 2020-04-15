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
                    ->where('display', true)
                    ->where(function ($q) use ($filial_id) {
                        $q->where('filial_id', null)->orWhere('filial_id', $filial_id);
                    })
                    ->orderBy('sort');
            }
        ]);

        return $view->with(compact('navigation'));
    }

}
