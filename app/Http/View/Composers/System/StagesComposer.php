<?php

namespace App\Http\View\Composers\System;

use App\Stage;
use Illuminate\View\View;

class StagesComposer
{
    /**
     * Эпаты
     */
    protected $stages;

    /**
     * StagesComposer constructor.
     */
    public function __construct()
    {
        $this->stages = Stage::get();
    }

    /**
     * Отдаем этапы на шаблон
     *
     * @param View $view
     * @return View
     */
	public function compose(View $view)
	{
		return $view->with('stages', $this->stages);
	}
}
