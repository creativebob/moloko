<?php

namespace App\Http\View\Composers\System;

use App\Metric;

use Illuminate\View\View;

class FiltersComposer
{
	public function compose(View $view)
	{

        $answer = operator_right('metrics', false, 'index');

        $filters = Metric::moderatorLimit($answer)
        ->companiesLimit($answer)
        ->get([
            'id',
            'name'
        ]);

        return $view->with(compact('filters'));

    }

}
