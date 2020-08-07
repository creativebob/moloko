<?php

namespace App\Http\View\Composers\System;

use App\City;
use Illuminate\View\View;

class CitiesComposer
{
    /**
     * Города
     */
    protected $cities;

    /**
     * CitiesComposer constructor.
     */
    public function __construct()
    {
        $this->cities = City::get();
    }

    /**
     * Отдаем города на шаблон
     *
     * @param View $view
     * @return View
     */
	public function compose(View $view)
	{
        return $view->with('cities', $this->cities);
    }

}
