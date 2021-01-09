<?php

namespace App\Http\View\Composers\System;

use App\ToolsType;
use Illuminate\View\View;

class ToolsTypesComposer
{

    /**
     * Типы оборудования
     */
    protected $toolsTypes;

    /**
     * Отдаем на шаблон
     *
     * @param View $view
     * @return View
     */
	public function compose(View $view)
	{
        $this->toolsTypes = ToolsType::get([
                'id',
                'name'
            ]);

        return $view->with('toolsTypes', $this->toolsTypes);
    }
}
