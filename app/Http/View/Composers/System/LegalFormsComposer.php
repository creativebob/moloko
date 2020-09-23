<?php

namespace App\Http\View\Composers\System;

use App\LegalForm;
use Illuminate\View\View;

class LegalFormsComposer
{
    /**
     * Формы регистрации
     */
    protected $legalForms;

    /**
     * LegalFormsComposer constructor.
     */
    public function __construct()
    {
        $this->legalForms = $legalFormsList = LegalForm::get();
    }

    /**
     * Отдаем на шаблон
     *
     * @param View $view
     * @return View
     */
    public function compose(View $view)
    {
        return $view->with('legalForms', $this->legalForms);
    }
}
