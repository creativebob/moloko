<?php

namespace App\Http\View\Composers\System;

use App\User;
use Illuminate\View\View;

class AuthorsComposer
{
    public function compose(View $view)
    {

        $authors = User::where('site_id', 1)
            ->get();

        return $view->with(compact('authors'));
    }

}
