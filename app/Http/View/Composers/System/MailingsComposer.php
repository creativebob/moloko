<?php

namespace App\Http\View\Composers\System;

use App\Mailing;
use Illuminate\View\View;

class MailingsComposer
{

    /**
     * Рассылки
     */
    protected $mailings;

    /**
     * Отдаем на шаблон
     *
     * @param View $view
     * @return View
     */
	public function compose(View $view)
	{
        $answer = operator_right('mailings', false, 'index');

        $manual = isset($view->manual);

        $this->mailings = Mailing::moderatorLimit($answer)
            ->companiesLimit($answer)
            ->authors($answer)
            ->when($manual, function ($q) {
                $q->whereNull('mailing_list_id');
            })
            ->when(!$manual, function ($q) {
                $q->whereNotNull('mailing_list_id');
            })
            // ->systemItem($answer)
            ->get([
                'id',
                'name'
            ]);

        return $view->with('mailings', $this->mailings);
    }
}
