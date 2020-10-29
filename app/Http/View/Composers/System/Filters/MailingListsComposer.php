<?php

namespace App\Http\View\Composers\System\Filters;

use App\MailingList;
use Illuminate\View\View;

class MailingListsComposer
{

    /**
     * Списки рассылок
     */
    protected $mailingsLists;

    /**
     * MailingListsComposer constructor.
     */
	public function __construct()
	{
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('mailings_lists', false, 'index');

        $this->mailingsLists = MailingList::moderatorLimit($answer)
            ->systemItem($answer)
            ->companiesLimit($answer)
            ->get();
//         dd($mailingsLists);
    }

    /**
     * Отдаем списки рассылок на шаблон
     *
     * @param View $view
     * @return View
     */
    public function compose(View $view)
    {
        return $view->with('mailingsLists', $this->mailingsLists);
    }
}
