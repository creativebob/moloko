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
        $company = auth()->user()->company;

        $company->load([
            'organizations' => function ($q) {
                $q->with([
                    'client',
                    'representatives' => function ($q) {
                        $q->with([
                            'client'
                        ])
                            ->latest();
                    }
                ]);
            }
        ]);

        $this->companies = $company->organizations;
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
