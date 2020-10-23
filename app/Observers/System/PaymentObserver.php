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
        $payment->total = $payment->cash + $payment->electronically;

        if ($payment->cash > 0 && $payment->electronically == 0) {
            $payment->type = 'cash';
        }

        if ($payment->cash == 0 && $payment->electronically > 0) {
            $payment->type = 'electronically';
        }

        if ($payment->cash > 0 && $payment->electronically > 0) {
            $payment->type = 'mixed';
        }

        $payment->registered_at = now();

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
        $paid = $contract->paid + $payment->total;
        $debit = $contract->debit - $payment->total;

        $contract->update([
           'paid' => $paid,
           'debit' => $debit
        ]);
    }
}
