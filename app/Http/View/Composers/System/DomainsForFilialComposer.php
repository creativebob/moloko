<?php

namespace App\Http\View\Composers\System;

use App\Domain;
use Illuminate\View\View;

class DomainsForFilialComposer
{

    /**
     * Скидки
     */
    protected $domain;

    /**
     * DiscountsComposer constructor.
     */
    public function __construct()
    {

    }

    /**
     * Отдаем скидки на шаблон
     *
     * @param View $view
     * @return View
     */
    public function compose(View $view)
    {

        $filialId = $view->lead->filial_id;

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('discounts', true, 'index');

        // Главный запрос
        $this->domain = Domain::whereHas('filials', function ($q) use ($filialId) {
                $q->where('id', $filialId);
            })
            ->moderatorLimit($answer)
            ->companiesLimit($answer)
            ->first();

        return $view->with('domain', $this->domain);
    }
}
