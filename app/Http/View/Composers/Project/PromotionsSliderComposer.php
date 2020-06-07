<?php

namespace App\Http\View\Composers\Project;

use App\Models\Project\Promotion;
use Illuminate\View\View;

class PromotionsSliderComposer
{

    public function compose(View $view)
    {
        $site = $view->site;
        $prom = request()->prom;
//        dd($prom);

        $promotions = Promotion::company($site->company_id)
            ->site($site->id)
            ->where('is_slider', true)
            ->where('begin_date', '<=', today())
            ->where('end_date', '>=', today())
            ->display()
            ->whereHas('filials', function($q) use ($site) {
                $q->where('id', $site->filial->id);
            })
            ->whereNull('prom')
            ->when($prom, function ($q) use ($site, $prom) {
                $q->orWhere(function($q) use ($site, $prom) {
                    $q->display()
                        ->company($site->company_id)
                        ->whereHas('filials', function($q) use ($site) {
                            $q->where('id', $site->filial->id);
                        })
                        ->whereIn('prom', $prom);
                });
            })
            ->orderBy('sort')
            ->get();
//        dd($promotions);

        return $view->with(compact('promotions'));
    }
}
