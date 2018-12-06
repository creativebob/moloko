<?php

namespace App\Http\ViewComposers;

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
		$menus = Menu::whereHas('page.entities', function ($q) use ($entities_list) {
			$q->whereIn('entity_id', $entities_list);
		})
		->where('display', 1)
		->orWhere(function ($q) {
			$q->whereNull('page_id')->where(['navigation_id' => 2, 'display' => 1]);
		})
		->orderBy('sort', 'asc')
		->get();
		// dd($menus->keyBy('name'));

		$sidebar = buildSidebarTree($menus);
		// dd($sidebar);

		return $view->with('sidebar', $sidebar);
	}

}