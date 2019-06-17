<?php

namespace App\Http\ViewComposers\Project;

use Illuminate\View\View;

class NavigationsComposer
{
	public function compose(View $view)
	{

        $site = $view->site->load(['navigations' => function ($q) {
        	$q->with([
        		'align',
        		'menus' => function ($q) {
        			$q->with('page')
                    ->where('display', 1)
                    ->orderBy('sort');
        		}
        	])
        	->where('display', 1)
        	->orderBy('sort');
        }]);

        return $view->with('navigations', $site->navigations->groupBy('align.tag'));
    }

}