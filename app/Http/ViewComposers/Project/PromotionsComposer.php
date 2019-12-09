<?php

namespace App\Http\ViewComposers\Project;

use Illuminate\View\View;

class PromotionsComposer
{

    public function compose(View $view)
    {
        $site = $view->site;
        $site->filial->load([
            'promotions' => function ($q) {
                $q->where('display', true)
                    ->where('begin_date', '<=', now())
                    ->where('end_date', '>=', now())
                    ->orderBy('sort');
            }
        ]);
        $promotions = $site->filial->promotions;

        return $view->with(compact('promotions'));
    }

}
