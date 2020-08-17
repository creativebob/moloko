<?php

namespace App\Http\View\Composers\System;

use App\Discount;
use Illuminate\View\View;

class DiscountsComposer
{

    /**
     * Скидки
     */
    protected $discounts;

    /**
     * DiscountsComposer constructor.
     */
    public function __construct()
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('discounts', true, 'index');

        // Главный запрос
        $this->discounts = Discount::where('archive', false)
            ->moderatorLimit($answer)
            ->companiesLimit($answer)
            ->get();
    }

    /**
     * Отдаем скидки на шаблон
     *
     * @param View $view
     * @return View
     */
    public function compose(View $view)
    {
        return $view->with('discounts', $this->discounts);
    }
}
