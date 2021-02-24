<?php

namespace App\Observers\System;

use App\Outlet;
use App\Shift;

class ShiftObserver extends BaseObserver
{
    /**
     * Handle the shift "creating" event.
     *
     * @param Shift $shift
     */
    public function creating(Shift $shift)
    {
        $this->store($shift);

        if ($shift->outlet_id) {
            $shift->need_closed_at = $shift->opened_at->addHours(23)->addMinutes(59)->addSeconds(59);
        }
    }

    /**
     * Handle the shift "updating" event.
     *
     * @param Shift $shift
     */
    public function updating(Shift $shift)
    {
        $this->update($shift);
    }

    /**
     * Handle the shift "updated" event.
     *
     * @param Shift $shift
     */
    public function updated(Shift $shift)
    {
        if ($shift->is_opened) {
            if ($shift->outlet_id) {
                $answer = operator_right(Shift::ALIAS, Shift::DEPENDENCE, getmethod('index'));

                $openFilialShift = Shift::
//            moderatorLimit($answer)
//                ->
                companiesLimit($answer)
                    ->authors($answer)
                    ->systemItem($answer)
                    ->whereDate('date', $shift->date)
                    ->where('filial_id', $shift->filial_id)
                    ->whereNull('outlet_id')
                    ->where('is_opened', true)
                    ->first();

                if ($openFilialShift) {

                    $openShifts = Shift::
//            moderatorLimit($answer)
//                ->
                    companiesLimit($answer)
                        ->authors($answer)
                        ->systemItem($answer)
                        ->whereDate('date', $shift->date)
                        ->where('filial_id', $shift->filial_id)
                        ->whereNotNull('outlet_id')
                        ->where('is_opened', true)
                        ->get([
                            'balance_open',
                            'cash',
                            'electronically',
                            'balance_close'
                        ]);

                    $openFilialShift->update([
                        'balance_open' => $openShifts->sum('balance_open'),
                        'cash' => $openShifts->sum('cash'),
                        'electronically' => $openShifts->sum('electronically'),
                        'balance_close' => $openShifts->sum('balance_open') + $openShifts->sum('cash')
                    ]);

                    $openCompanyShift = Shift::
//            moderatorLimit($answer)
//                ->
                    companiesLimit($answer)
                        ->authors($answer)
                        ->systemItem($answer)
                        ->whereDate('date', $shift->date)
                        ->whereNull('outlet_id')
                        ->whereNull('filial_id')
                        ->where('is_opened', true)
                        ->first();

                    if ($openCompanyShift) {

                        $openFilialsShifts = Shift::
//            moderatorLimit($answer)
//                ->
                        companiesLimit($answer)
                            ->authors($answer)
                            ->systemItem($answer)
                            ->whereDate('date', $shift->date)
                            ->whereNull('outlet_id')
                            ->whereNotNull('filial_id')
                            ->where('is_opened', true)
                            ->get([
                                'balance_open',
                                'cash',
                                'electronically',
                                'balance_close'
                            ]);

                        $openCompanyShift->update([
                            'balance_open' => $openFilialsShifts->sum('balance_open'),
                            'cash' => $openFilialsShifts->sum('cash'),
                            'electronically' => $openFilialsShifts->sum('electronically'),
                            'balance_close' => $openFilialsShifts->sum('balance_open') + $openFilialsShifts->sum('cash')
                        ]);
                    }
                }
            }
        }
    }
}
