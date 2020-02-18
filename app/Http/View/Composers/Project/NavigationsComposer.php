<?php

namespace App\Http\View\Composers\Project;

use Illuminate\View\View;

class NavigationsComposer
{
    public function compose(View $view)
    {
        $filial_id = $view->site->filial->id;

        $site = $view->site->load(['navigations' => function ($q) use ($filial_id) {
            $q->with([
                'align',
                'menus' => function ($q) use ($filial_id) {
                    $q->with('page')
                    ->where('display', true)

                    ->where(function ($query) use ($filial_id) {
                        $query->where('filial_id', null)->orWhere('filial_id', $filial_id);
                    })

                    ->orderBy('sort');
                }
            ])
            ->where('display', true)
            ->orderBy('sort');
        }]);

        // dd($site->navigations->where('alias', 'super'));
        return $view->with('navigations', $site->navigations->groupBy('align.tag'));
    }

}
