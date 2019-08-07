<?php

namespace App\Http\ViewComposers\System;

use App\Menu;

use Illuminate\View\View;

class SidebarComposer
{
	public function compose(View $view)
	{

		// Получаем список сущностей из сессии
		$session = app('session')->get('access');
		$entities_list = isset($session['settings']['entities_list']) ? $session['settings']['entities_list'] : [] ;
        // dd($entities_list);

        // Получаем меню (знаем что статика, поэтому указываем в таблице навигации id)
		$menus = Menu::whereHas('page.entity', function ($q) use ($entities_list) {
			$q->whereIn('id', $entities_list);
		})
		->where('display', true)
		->orWhere(function ($q) {
			$q->whereNull('page_id')->where(['navigation_id' => 1, 'display' => 1]);
		})
		->orderBy('sort', 'asc')
		->get();
		// dd($menus->keyBy('name'));

		$sidebar = buildSidebarTree($menus);
		// dd($sidebar);

		return $view->with('sidebar', $sidebar);
	}

}
