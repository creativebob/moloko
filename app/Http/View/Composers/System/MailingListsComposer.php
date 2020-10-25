<?php

namespace App\Http\View\Composers\System;

use App\MailingList;
use Illuminate\View\View;

class MailingListsComposer
{
    /**
     * Списки рассылок
     */
    protected $mailingLists;

    /**
     * Отдаем этапы на шаблон
     *
     * @param View $view
     * @return View
     */
	public function compose(View $view)
	{
        $answer = operator_right('mailing_lists', false, 'index');
        
        $this->mailingLists = MailingList::moderatorLimit($answer)
             ->companiesLimit($answer)
             ->authors($answer)
             ->template($answer)
             ->systemItem($answer)
            ->oldest('sort')
            ->get();
        
		return $view->with('mailingLists', $this->mailingLists);
	}
}
