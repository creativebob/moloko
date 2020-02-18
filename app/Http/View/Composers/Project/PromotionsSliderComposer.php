<?php

namespace App\Http\View\Composers\Project;

use Illuminate\View\View;

class PromotionsSliderComposer
{

    public function compose(View $view)
    {
        $site = $view->site;
        $site->filial->load([
            'promotions' => function ($q) {
                $q->where('display', true)
                    ->where('is_slider', true)
                    ->where('begin_date', '<=', now())
                    ->where('end_date', '>=', now())
                    ->orderBy('sort');
            }
        ]);
        $promotions = $site->filial->promotions;

        return $view->with(compact('promotions'));
    }

}
