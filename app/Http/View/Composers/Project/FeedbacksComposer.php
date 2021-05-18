<?php

namespace App\Http\View\Composers\Project;

use App\Feedback;
use Illuminate\View\View;

class FeedbacksComposer
{

    public function compose(View $view)
    {
        $feedbacks= Feedback::where('company_id', $view->site->company_id)
            ->get();

        return $view->with(compact('feedbacks'));
    }

}
