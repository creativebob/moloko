<?php

namespace App\Http\View\Composers\System;

use App\Lead;
use App\LegalForm;
use Illuminate\View\View;

class LeadHistoryComposer
{
    /**
     * Формы регистрации
     */
    protected $leadHistory;

    /**
     * Отдаем на шаблон
     *
     * @param View $view
     * @return View
     */
    public function compose(View $view)
    {
        $lead = $view->lead;

        if ($lead->draft == false) {
            // Получаем из сессии необходимые данные (Функция находиться в Helpers)
            $answer = operator_right('leads', true, getmethod('edit'));

            // История лида
            $this->leadHistory = Lead::with([
                'location.city',
                'choice',
                'manager',
                'stage',
                'user',
                'challenges.challenge_type',
                'phones',
            ])
                ->companiesLimit($answer)
                // ->authors($answer_lead) // Не фильтруем по авторам
                ->systemItem($answer) // Фильтр по системным записям
                // ->whereNull('archive')
                ->where('draft', false)
                ->whereHas('phones', function ($query) use ($lead) {
                    $query->where('phone', $lead->main_phone->phone);
                })
                ->where('id', '!=', $lead->id)
                ->oldest('sort')
                ->get();
        }
        return $view->with('leadHistory', $this->leadHistory);
    }
}
