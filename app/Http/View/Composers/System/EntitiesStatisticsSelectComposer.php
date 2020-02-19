<?php

namespace App\Http\View\Composers\System;

use App\Entity;

use Illuminate\View\View;

class EntitiesStatisticsSelectComposer
{
	public function compose(View $view)
	{

        // Главный запрос
        $entities = Entity::whereStatistic(true)
        ->orderBy('sort', 'asc')
        ->get(['id', 'name']);

        return $view->with('entities', $entities);
    }

}
