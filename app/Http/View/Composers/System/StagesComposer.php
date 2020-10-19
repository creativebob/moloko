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
     * Отдаем этапы на шаблон
     *
     * @param View $view
     * @return View
     */
	public function compose(View $view)
	{
        $answer = operator_right('stages', false, 'index');
        
        $this->stages = Stage::moderatorLimit($answer)
            // ->companiesLimit($answer)
            // ->authors($answer)
            // ->template($answer)
            // ->systemItem($answer) // Фильтр по системным записям
            ->oldest('sort')
            ->get();
		return $view->with('stages', $this->stages);
	}
}
