<?php

namespace App\Http\View\Composers\System;

use App\Site;

use Illuminate\View\View;

class SitesComposer
{

    /**
     * Сайты
     */
    protected $sites;

    /**
     * SitesComposer constructor.
     */
    public function __construct()
    {
        $answer = operator_right('sites', false, 'index');

        $this->sites = Site::moderatorLimit($answer)
            ->companiesLimit($answer)
            ->authors($answer)
//        ->orWhereNull('company_id')
            // ->systemItem($answer)
            ->get([
                'id',
                'name'
            ]);
    }

    /**
     * Отдаем на шаблон
     *
     * @param View $view
     * @return View
     */
	public function compose(View $view)
	{
        return $view->with('sites', $this->sites);
    }
}
