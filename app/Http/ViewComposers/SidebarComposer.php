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
		$entities_list = $session['settings']['entities_list'];
		if (empty($entities_list)) {
			$entities_list = [];
		}
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

		$sidebar = $this->buildSidebarTree($menus);

		return $view->with('sidebar_tree', $sidebar);
	}

	public function buildSidebarTree($items)
	{

		$grouped = $items->groupBy('parent_id');

		foreach ($items as $item) {
			if ($grouped->has($item->id)) {
				$item->childrens = $grouped[$item->id];
			}
		}

		return $items->where('parent_id', null);
	}
}