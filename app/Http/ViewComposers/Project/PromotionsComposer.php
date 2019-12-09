<?php

namespace App\Http\ViewComposers\Project;

use App\Promotion;
use Illuminate\Support\Facades\Cookie;
use Illuminate\View\View;

class PromotionsComposer
{

    public function compose(View $view)
    {

        $promotions = Promotion::where([
            'filial_id' => $view->site->filial->id,
            'display' => true
        ])
            ->where('begin_date', '<', now())
            ->where('end_date', '>', now())
            ->orderBy('sort')
            ->get();
//        $site = $view->site->load('filials.promotions');
//        $promotions = $site->filials->first()->promotions->where('filial_id', 1)->where('begin_date', '<', now())->where('end_date', '>', now())->where('display', true)->sortBy('sort');

        return $view->with(compact('promotions'));
    }

}
