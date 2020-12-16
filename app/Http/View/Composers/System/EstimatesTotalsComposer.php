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
            // ->template($answer)
            ->systemItem($answer)
            ->where('draft', false)
            ->whereNotNull('registered_at')
            ->filter()
            ->select(\DB::raw('SUM(amount) AS amount, SUM(discount_currency) AS discount_currency, SUM(total) AS total, SUM(share_currency) AS share_currency, SUM(principal_currency) AS principal_currency'))
            ->first();

        $company_id = \Auth::user()->company_id;


        // TODO: 17.12.2020 / Плохое решение - Получение агрегированных значений по доли партнера (Делаеться через два дополнительных запроса средствами MySQL, но делать через перебор коллекции еще хуже. Нужно найти решение.)


        // Агрегация вознаграждения текущей компании как агента
        $agent_self = Estimate::moderatorLimit($answer)
            ->companiesLimit($answer)
            ->authors($answer)
            // ->template($answer)
            ->systemItem($answer)
            ->where('draft', false)
            ->whereNotNull('registered_at')
            ->filter()
            ->whereHas('agent', function($q) use ($company_id){
                $q->where('agent_id', $company_id);
            })
            ->select(\DB::raw('SUM(principal_currency) AS partner_currency'))
            ->first();

        // Агрегация вознаграждения текущей компании как принципала
        $principal_self = Estimate::moderatorLimit($answer)
            ->companiesLimit($answer)
            ->authors($answer)
            // ->template($answer)
            ->systemItem($answer)
            ->where('draft', false)
            ->whereNotNull('registered_at')
            ->filter()
            ->whereHas('agent', function($q) use ($company_id){
                $q->where('agent_id', '!=', $company_id);
            })
            ->select(\DB::raw('SUM(share_currency) AS partner_currency'))
            ->first();

        $this->estimatesTotals->partner_currency = $principal_self->partner_currency + $agent_self->partner_currency;

        return $view->with('estimatesTotals', $this->estimatesTotals);
    }
}
