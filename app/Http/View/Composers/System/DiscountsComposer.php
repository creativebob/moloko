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
    
    }

    /**
     * Отдаем скидки на шаблон
     *
     * @param View $view
     * @return View
     */
    public function compose(View $view)
    {
        $entityAlias = $view->entity;
    
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('discounts', true, 'index');
    
        // Главный запрос
        $this->discounts = Discount::where('archive', false)
            ->whereHas('entity', function ($q) use ($entityAlias) {
                $q->where('alias', $entityAlias);
            })
            ->moderatorLimit($answer)
            ->companiesLimit($answer)
            ->get();
        
        return $view->with('discounts', $this->discounts);
    }
}
