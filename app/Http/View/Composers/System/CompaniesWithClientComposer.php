<?php

namespace App\Http\View\Composers\System;

use App\Company;
use Illuminate\View\View;

class CompaniesWithClientComposer
{
    /**
     * Компании
     */
    protected $companies;

    /**
     * CompaniesComposer constructor.
     */
    public function __construct()
    {
        $this->companies = Company::with([
            'client',
            'representatives.client'
        ])
        ->get();
    }

    /**
     * Отдаем на шаблон
     *
     * @param View $view
     * @return View
     */
    public function compose(View $view)
    {
        return $view->with('companies', $this->companies);
    }
}
