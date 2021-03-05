<?php

namespace App\Http\View\Composers\System;

use App\Entity;
use Illuminate\View\View;

class ProcessesArchivesCountComposer
{
	public function compose(View $view)
	{

        $res = strpos(request()->url(), 'archives');
	    if (! $res) {
            // Получаем из сессии необходимые данные (Функция находиться в Helpers)
            $answer = operator_right($view->entity, false, 'index');

            $model = Entity::where('alias', $view->entity)
                ->value('model');

            $archivesCount = $model::moderatorLimit($answer)
                ->companiesLimit($answer)
                ->authors($answer)
                ->systemItem($answer)

                // ->filter($request, 'goods_product_id', 'article')
                ->where('archive', true)
                ->count();

            return $view->with(compact('archivesCount'));
        }

    }

}
