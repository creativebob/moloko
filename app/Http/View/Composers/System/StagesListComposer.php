<?php

namespace App\Http\View\Composers\System;

use App\Stage;

use Illuminate\View\View;

class StagesListComposer
{
	public function compose(View $view)
	{

        $answer_stages = operator_right('stages', false, 'index');

		$stages_list = Stage::moderatorLimit($answer_stages)
        // ->companiesLimit($answer_stages)
        // ->authors($answer_stages)
        // ->template($answer_stages)
        // ->systemItem($answer_stages) // Фильтр по системным записям
        ->where('display', true)
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->get()->pluck('name', 'id');

		return $view->with('stages_list', $stages_list);

	}

}
