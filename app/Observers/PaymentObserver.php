<?php

namespace App\Observers;

use App\Payment;

use App\Observers\Traits\Commonable;

class PaymentObserver
{

    use Commonable;

    public function creating(Payment $payment)
    {
        $this->store($payment);
    }

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

    public function updating(Payment $payment)
    {
        $this->update($payment);
    }

    public function deleting(Payment $payment)
    {
        $this->destroy($payment);
    }
}
