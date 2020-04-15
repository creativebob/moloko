<?php

namespace App\Http\View\Composers\Project;

use Illuminate\View\View;

class NavigationByAlignComposer
{
    public function compose(View $view)
    {
        $align = $view->align ?? null;
        $filial_id = $view->site->filial->id;

        $site = $view->site->load([
            'navigations' => function ($q) use ($filial_id, $align) {
                $q->with([
                    'align',
                    'menus' => function ($q) use ($filial_id) {
                        $q->with([
                            'page'
                        ])
                            ->where('display', true)
                            ->where(function ($query) use ($filial_id) {
                                $query->where('filial_id', null)
                                    ->orWhere('filial_id', $filial_id);
                            })
                            ->orderBy('sort');
                    }
                ])
                    ->whereHas('align', function ($q) use ($align) {
                        $q->where('tag', $align);
                    })
                ->where('display', true)
                ->orderBy('sort');
            }
        ]);
        $navigation = $view->site->navigations->first();

        return $view->with(compact('navigation'));
    }

}
