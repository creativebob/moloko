<?php

namespace App\Observers\System;

use App\Lead;
use App\Observers\System\Traits\Commonable;

class LeadObserver
{

    use Commonable;

    public function creating(Lead $lead)
    {
        $this->store($lead);

        $user = auth()->user();
        $lead->filial_id = $user->stafferFilialId;
        $lead->manager_id = $user->id;

        // TODO - 23.09.20 - Умолчания лечатся умолчаниями в БД
        $lead->stage_id = 2;
        $lead->lead_type_id = 1;
        $lead->lead_method_id = 1;
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

        $lead->draft = false;
    }

    public function deleting(Lead $lead)
    {
        $this->destroy($lead);
    }
}
