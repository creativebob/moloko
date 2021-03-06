<?php

namespace App\Observers\System;

use App\Department;
use App\Lead;
use App\Observers\System\Traits\Commonable;

class LeadObserver
{

    use Commonable;

    public function creating(Lead $lead)
    {
        $this->store($lead);

        $user = auth()->user();

        // Вписываем филилал и торговую точку

        $outlets = session("access.user_info.outlets");
        if ($outlets) {
            $outlet = $outlets[0];

            $lead->filial_id = $outlet->filial_id;
            $lead->outlet_id = $outlet->id;
        } else {
            $filialId = session('access.all_rights.index-leads-allow.filials_for_user')[0]->id;
            $filial = Department::with([
                'outlets'
            ])
                ->find($filialId);

            $lead->filial_id = $filial->id;
            $lead->outlet_id = $filial->outletId;
        }

        $lead->manager_id = $user->id;

        // TODO - 23.09.20 - Умолчания лечатся умолчаниями в БД
        $lead->stage_id = 2;
        $lead->lead_type_id = 1;
        $lead->lead_method_id = 1;

        $lead->draft = true;

        // TODO - 08.12.20 - Долгий путь с затычками (вылечится рабочим местом)
        $outlets = session('access.user_info.outlets');
        if ($outlets) {
            $outlet = $outlets[0];
            if ($outlet->settings->firstWhere('alias', 'shipment_at-calculate')) {
                $lead->shipment_at = now()->addSeconds($outlet->extra_time);
            }
        }
    }

    public function created(Lead $lead)
    {
        // TODO - 23.09.20 - Разобраться в функции, перенести в обсервер, т.к. номера вписываются только при записи
        $lead_number = getLeadNumbers(auth()->user(), $lead);
        $lead->case_number = $lead_number['case'];
        $lead->serial_number = $lead_number['serial'];
        $lead->save();
    }

    public function updating(Lead $lead)
    {
        $this->update($lead);

        if ($lead->name) {
            $lead->draft = false;
        }
    }

    public function deleting(Lead $lead)
    {
        $this->destroy($lead);
    }
}
