<?php

namespace App\Http\View\Composers\System;

use App\TaxationType;
use Illuminate\View\View;

class TaxationTypesComposer
{

    /**
     * Системы налогообложения
     */
    protected $taxationTypes;

    /**
     * TaxationTypesComposer constructor.
     */
    public function __construct()
    {

        $this->taxationTypes = TaxationType::toBase()
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
        return $view->with('taxationTypes', $this->taxationTypes);
    }
}
