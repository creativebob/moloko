<?php

namespace App\Http\View\Composers\System;

use App\LeadMethod;
use Illuminate\View\View;

class LeadMethodsComposer
{
    /**
     * Методы лида
     */
    protected $leadMethods;

    /**
     * LeadMethodsComposer constructor.
     */
    public function __construct()
    {
        $this->leadMethods = LeadMethod::get();
    }

    /**
     * Отдаем методы лида на шаблон
     *
     * @param View $view
     * @return View
     */
	public function compose(View $view)
	{
		return $view->with('leadMethods', $this->leadMethods);
	}
}
