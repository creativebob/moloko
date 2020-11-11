<?php

namespace App\Http\View\Composers\System;

use App\Template;
use Illuminate\View\View;

class TemplatesComposer
{
    /**
     * Шаблоны
     */
    protected $templates;

    /**
     * Отдаем этапы на шаблон
     *
     * @param View $view
     * @return View
     */
	public function compose(View $view)
	{
        $answer = operator_right('templates', false, 'index');

        $this->templates = Template::moderatorLimit($answer)
             ->companiesLimit($answer)
             ->authors($answer)
             ->template($answer)
             ->systemItem($answer)

            // TODO - 09.11.20 - Внедрить алиас в категории шаблонов
            ->where('category_id', $view->categoryId)

            ->oldest('sort')
            ->get();

		return $view->with('templates', $this->templates);
	}
}
