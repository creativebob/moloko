<?php

namespace App\Http\ViewComposers\Project;

use Illuminate\View\View;
use App\Rubricator;
use App\News;

class NewsComposer
{
	public function compose(View $view)
	{
        $company_id = $view->site->company_id;

        $rubricator = Rubricator::with('items')
        ->where(['display' => true])
        ->first();

		// Получаем все доступные разделы прайса
        $rubricator_items_ids = $rubricator->items_public->pluck('id');

        $news_list = News::whereIn('rubricators_item_id', $rubricator_items_ids)
        ->where([
        	'display' => true,
        ])
        ->orderBy('sort', 'asc')
        ->paginate(16);

        return $view->with('news_list', $news_list);
    }
}