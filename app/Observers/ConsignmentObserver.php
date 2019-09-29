<?php

namespace App\Observers;

use App\Consignment;
use App\Observers\Traits\Commonable;
use Carbon\Carbon;

class ConsignmentObserver
{

    use Commonable;

    public function creating(Consignment $consignment)
    {
        $this->store($consignment);
        $consignment->receipt_date = Carbon::now()->format('d.m.Y');
        $consignment->draft = true;

        $user = request()->user();
        $consignment->filial_id = $user->filial_id;
    }

    public function updating(Consignment $consignment)
    {
        $this->update($consignment);
    }

    public function deleting(Consignment $consignment)
    {
        $this->destroy($consignment);
    }

}
