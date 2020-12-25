<?php

namespace App\Http\View\Composers\System;

use Illuminate\View\View;

class FilialsForUserComposer
{
	public function compose(View $view)
	{
        if ($view->entity) {
            $entity = $view->entity;
        } else {
            $entity = 'users';
        }
		$filial_list = getLS($entity, 'view', 'filials');
		return $view->with('filial_list', $filial_list);

	}

}
