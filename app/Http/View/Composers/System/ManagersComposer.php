<?php

namespace App\Http\View\Composers\System;

use App\User;
use Illuminate\View\View;

class ManagersComposer
{
    /**
     * Менеджеры
     */
    protected $managers;

    /**
     * ManagersComposer constructor.
     */
    public function __construct()
    {
        $this->managers = User::where(function ($q) {
            $q->where('site_id', 1)
                ->orWhere('id', 1);
            })
            ->has('leads_control')
            ->orderBy('name')
            ->get();
    }

    /**
     * Отдаем менеджеров на шаблон
     *
     * @param View $view
     * @return View
     */
	public function compose(View $view)
	{
		return $view->with('managers', $this->managers);
	}
}
