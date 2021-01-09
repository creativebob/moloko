<?php

namespace App\Http\View\Composers\System;

use App\Entity;
use Illuminate\View\View;

class EntitiesForDiscountsComposer
{

    /**
     * Скидки
     */
    protected $entities;

    /**
     * EntitiesForDiscountsComposer constructor.
     */
    public function __construct()
    {
        $entities = [
            'prices_goods',
            'prices_services',
            'catalogs_goods_items',
            'catalogs_services_items',
            'estimates',
        ];

        // Главный запрос
        $this->entities = Entity::whereIn('alias', $entities)
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
        return $view->with('entities', $this->entities);
    }
}
