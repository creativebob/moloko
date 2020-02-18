<?php

namespace App\Http\View\Composers\Project;

use Illuminate\Support\Facades\Cookie;
use Illuminate\View\View;

class FilialComposer
{

    public function compose(View $view)
    {
        $filial = $view->site->filial;
        return $view->with(compact('filial'));
    }

}
