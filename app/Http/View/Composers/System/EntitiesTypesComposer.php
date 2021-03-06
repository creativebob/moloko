<?php

namespace App\Http\View\Composers\System;

use App\EntitiesType;
use Illuminate\View\View;

class EntitiesTypesComposer
{
    /**
     * Отдаем на шаблон
     *
     * @param View $view
     * @return View
     */
    public function compose(View $view)
    {
        $entitiesTypes = EntitiesType::toBase()
            ->get([
                'id',
                'name'
            ]);
//        dd($entitiesTypes);

        return $view->with(compact('entitiesTypes'));
    }
}
