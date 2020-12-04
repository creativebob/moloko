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

        $this->estimatesTotals['amount'] = Estimate::moderatorLimit($answer)
             ->companiesLimit($answer)
             ->authors($answer)
             ->template($answer)
             ->systemItem($answer)
            ->filter()
            ->sum('amount');

        $this->estimatesTotals['discount_currency'] = Estimate::moderatorLimit($answer)
            ->companiesLimit($answer)
            ->authors($answer)
            ->template($answer)
            ->systemItem($answer)
            ->filter()
            ->sum('discount_currency');

        $this->estimatesTotals['total'] = Estimate::moderatorLimit($answer)
            ->companiesLimit($answer)
            ->authors($answer)
            ->template($answer)
            ->systemItem($answer)
            ->filter()
            ->sum('total');

		return $view->with('estimatesTotals', $this->estimatesTotals);
	}
}
