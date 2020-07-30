<?php

namespace App\Http\View\Composers\Project;

use App\Models\Project\Promotion;
use Illuminate\View\View;

class PromotionsSliderComposer
{

    public function compose(View $view)
    {
        $site = $view->site;
        $prom = json_decode(\Cookie::get('prom'), true) ?? request()->prom;
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
                        ->where('is_slider', true)
                        ->where('begin_date', '<=', today())
                        ->where('end_date', '>=', today())

                        ->when(is_array($prom), function ($q) use ($prom) {
                            $q->whereIn('prom', $prom);
                        })
                        ->when(is_string($prom), function ($q) use ($prom) {
                            $q->where('prom', $prom);
                        });
                });
            })
            ->orderBy('sort')
            ->get();
//        dd($promotions);

        return $view->with(compact('promotions'));
    }
}
