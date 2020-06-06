<?php

namespace App\Http\View\Composers\Project;

use App\Models\Project\Promotion;
use Illuminate\View\View;

class PromotionsSliderComposer
{

    public function compose(View $view)
    {
        $site = $view->site;
        $param = optional($view)->param;

        $promotions = Promotion::company($site->company_id)
            ->where('is_slider', true)
            ->where('begin_date', '<=', today())
            ->where('end_date', '>=', today())
            ->display()
            ->whereHas('filials', function($q) use ($site) {
                $q->where('id', $site->filial->id);
            })
            ->whereNull('prom')
            ->when($param, function ($q) use ($site, $param) {
                $q->orWhere(function($q) use ($site, $param) {
                    $q->display()
                        ->company($site->company_id)
                        ->whereHas('filials', function($q) use ($site) {
                            $q->where('id', $site->filial->id);
                        })
                        ->whereIn('prom', $param);
                });
            })
            ->orderBy('sort')
            ->get();

        return $view->with(compact('promotions'));
    }
}
