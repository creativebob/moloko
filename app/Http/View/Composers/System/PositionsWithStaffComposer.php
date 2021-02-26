<?php

namespace App\Http\View\Composers\System;

use App\Position;
use Illuminate\View\View;

class PositionsWithStaffComposer
{
    public function compose(View $view)
    {
        $answer = operator_right('positions', false, 'index');

        $positions = Position::has('staff')
            ->moderatorLimit($answer)
            ->companiesLimit($answer)
            ->authors($answer)
            ->systemItem($answer)
            ->where('archive', false)
            //		->template($answer)
            ->get();

        return $view->with(compact('positions'));
    }
}
