<?php

namespace App\Http\View\Composers\System;

use App\Discount;
use Illuminate\View\View;

class DiscountsForEstimatesComposer
{
    public function compose(View $view)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('discounts', true, 'index');
        
        $discount = Discount::where('archive', false)
            ->whereHas('entity', function ($q) {
                $q->where('alias', 'estimates');
            })
            ->where('begined_at', '<=', now())
            ->where(function ($q) {
                $q->where('ended_at', '>=', now())
                    ->orWhereNull('ended_at');
            })
            ->moderatorLimit($answer)
            ->companiesLimit($answer)
            ->first();

        return $view->with(compact('discount'));
    }
}
