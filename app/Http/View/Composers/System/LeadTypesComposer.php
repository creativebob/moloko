<?php

namespace App\Http\View\Composers\System;

use App\LeadType;
use Illuminate\View\View;

class LeadTypesComposer
{
    /**
     * Типы лида
     */
    protected $leadTypes;

    /**
     * LeadTypesComposer constructor.
     */
    public function __construct()
    {
        $this->leadTypes = LeadType::get();
    }

    /**
     * Отдаем типы лида на шаблон
     *
     * @param View $view
     * @return View
     */
	public function compose(View $view)
	{
		return $view->with('leadTypes', $this->leadTypes);
	}
}
