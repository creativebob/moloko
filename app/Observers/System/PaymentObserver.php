<?php

namespace App\Observers\System;

use App\Models\System\Documents\Estimate;
use App\Payment;
use App\Shift;

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

        if (empty($payment->registered_at)) {
            $payment->registered_at = now();
        }

        if ($payment->document_type == 'App\Models\System\Documents\Estimate') {
            $estimate = Estimate::with([
                'lead'
            ])
                ->find($payment->document_id);
            $outletId = $estimate->lead->outlet_id;

            $answer = operator_right('shifts', true, getmethod('index'));

            $openShift = Shift::
//            moderatorLimit($answer)
//                ->
            companiesLimit($answer)
                ->authors($answer)
                ->systemItem($answer)
//                ->whereDate('date', today()->format('Y-m-d'))
                ->where('outlet_id', $outletId)
                ->where('is_opened', true)
                ->where('need_closed_at', '>=', now())
                ->first();

            if ($openShift) {
                $payment->shift_id = $openShift->id;
            }
        }
    }

    /**
     * Handle the payment "created" event.
     *
     * @param Payment $payment
     */
    public function created(Payment $payment)
    {
        $payment->load([
            'contract',
            'sign',
            'document.payments'
        ]);

        if ($payment->document_type == 'App\Models\System\Documents\Estimate') {
            $estimate = $payment->document;

            $type = null;
            $cash = $estimate->payments->sum('cash');
            $electronically = $estimate->payments->sum('electronically');

            if ($cash > 0 && $electronically > 0) {
                $type = 'mixed';
            }

            if ($cash > 0 && $electronically == 0) {
                $type = 'cash';
            }

            if ($cash == 0 && $electronically > 0) {
                $type = 'electronically';
            }

            $paid = $cash + $electronically;

            $estimate->update([
                'paid' => $paid,
                'debit' => $estimate->total - $paid,
                'payment_type' => $type
            ]);
        }

        $contract = $payment->contract;

        // TODO - 06.02.20 - Нужна проверка на отрицательные значения, обновление договора в обсервере возможно
        switch ($payment->sign->alias) {
            case ('sell'):

                $paid = $contract->paid + $payment->total;
                $debit = $contract->debit - $payment->total;
                break;

            case ('sellReturn'):
                $paid = $contract->paid - $payment->total;
                $debit = $contract->debit + $payment->total;
                break;
        }

        $contract->update([
            'paid' => $paid,
            'debit' => $debit
        ]);

        // Если есть торговая точка, то агрегируем
        if ($payment->shift_id) {
            $payment->load([
                'shift',
            ]);
            $shift = $payment->shift;

            $payments = Payment::where('shift_id', $shift->id)
                ->get([
                    'cash',
                    'electronically'
                ]);

            $shift->update([
                'cash' => $payments->sum('cash'),
                'electronically' => $payments->sum('electronically'),
                'balance_close' => $shift->balance_open + $payments->sum('cash')
            ]);
        }
    }

    /**
     * Handle the payment "updating" event.
     *
     * @param Payment $payment
     */
    public function updating(Payment $payment)
    {
        $this->update($payment);
    }
}
