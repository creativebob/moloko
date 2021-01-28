<?php

namespace App\Http\View\Composers\System;

use App\Entity;
use Illuminate\View\View;

class EntitiesComposer
{

    /**
     * Сущности
     */
    protected $entities;

    /**
     * EntitiesComposer constructor.
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
        $this->entities = Entity::toBase()
        ->get([
            'id',
            'name'
        ]);
    }

    /**
     * Отдаем на шаблон
     *
     * @param View $view
     * @return View
     */
    public function compose(View $view)
    {
        return $view->with('entities', $this->entities);
    }
}
