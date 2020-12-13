<?php

namespace App\Http\View\Composers\System;

use App\Models\System\Documents\Estimate;
use Illuminate\View\View;

class EstimatesTotalsComposer
{
    /**
     * Списки рассылок
     */
    protected $estimatesTotals;

    /**
     * Отдаем этапы на шаблон
     *
     * @param View $view
     * @return View
     */
    public function compose(View $view)
    {
        $answer = operator_right('estimates', false, 'index');

        $this->estimatesTotals = Estimate::moderatorLimit($answer)
            ->companiesLimit($answer)
            ->authors($answer)
            ->template($answer)
            ->systemItem($answer)
            ->where('draft', false)
            ->whereNotNull('registered_at')
            ->filter()
            ->select(\DB::raw('SUM(amount) AS amount, SUM(discount_currency) AS discount_currency, SUM(total) AS total, SUM(share_currency) AS share_currency, SUM(principal_currency) AS principal_currency'))
            ->first();
//        dd($this->estimatesTotals);

        return $view->with('estimatesTotals', $this->estimatesTotals);
    }
}
