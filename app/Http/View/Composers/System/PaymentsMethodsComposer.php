<?php

namespace App\Http\View\Composers\System;

use App\PaymentsMethod;
use Illuminate\View\View;

class PaymentsMethodsComposer
{

    /**
     * Рассылки
     */
    protected $paymentsMethods;

    /**
     * PaymentsMethodsComposer constructor.
     */
    public function __construct()
    {
        $this->paymentsMethods = PaymentsMethod::get([
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
        return $view->with('paymentsMethods', $this->paymentsMethods);
    }
}
