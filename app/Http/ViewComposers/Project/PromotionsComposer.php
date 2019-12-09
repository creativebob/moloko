<?php

namespace App\Http\ViewComposers\Project;

use Illuminate\View\View;

class PromotionsComposer
{

    public function compose(View $view)
    {

        $site = $view->site->load('filial.promotions');
        $promotions = $site->filial->promotions->where('filial_id', 1)
            ->where('begin_date', '<', now())
            ->where('end_date', '>', now())
            ->where('display', true)
            ->sortBy('sort');

        return $view->with(compact('promotions'));
    }

}
