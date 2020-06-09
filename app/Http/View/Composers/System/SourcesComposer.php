<?php

namespace App\Http\View\Composers\System;

use App\Source;
use Illuminate\View\View;

class SourcesComposer
{
	public function compose(View $view)
	{
        $sources = Source::get();
        return $view->with(compact('sources'));
    }
}
