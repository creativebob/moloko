<?php

namespace App\Http\ViewComposers;

use App\LegalForm;

use Illuminate\View\View;

class LegalFormsSelectComposer
{
	public function compose(View $view)
	{

        $legal_forms_list = LegalForm::get()->pluck('name', 'id');
		return $view->with('legal_forms_list', $legal_forms_list);

	}

}