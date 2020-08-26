<?php

namespace App\Http\View\Composers\Project;

use App\Discount;
use Illuminate\View\View;

class DiscountsForEstimatesComposer
{
    public function compose(View $view)
    {
        $site = $view->site;
        $discount = Discount::where([
            'company_id' => $site->company_id,
            'display' => true,
            'archive' => false
        ])
            ->whereHas('entity', function ($q) {
                $q->where('alias', 'estimates');
            })
            ->where('begined_at', '<=', now())
            ->where(function ($q) {
                $q->where('ended_at', '>=', now())
                    ->orWhereNull('ended_at');
            })
            ->first();

        return $view->with(compact('discount'));
    }
}
