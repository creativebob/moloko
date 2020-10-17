<?php

namespace App\Observers\System;

use App\Payment;

class PaymentObserver extends BaseObserver
{
    
    /**
     * Handle the payment "creating" event.
     *
     * @param Payment $payment
     */
    public function creating(Payment $payment)
    {
        $this->store($payment);
    }
    
    /**
     * Handle the payment "created" event.
     *
     * @param Payment $payment
     */
    public function created(Payment $payment)
    {
        $contract = $payment->contract;

        // TODO - 06.02.20 - Нужна проверка на отрицательные значения, обновление договора в обсервере возможно
        $paid = $contract->paid + $payment->amount;
        $debit = $contract->debit - $payment->amount;

        $contract->update([
           'paid' => $paid,
           'debit' => $debit
        ]);
    }
}
