<?php

namespace App\Observers\System;

use App\Consignment;
use App\Observers\System\Traits\Commonable;
use App\Stock;

class ConsignmentObserver
{

    use Commonable;

    public function creating(Consignment $consignment)
    {
        $this->store($consignment);
        $consignment->receipt_date = now()->format('d.m.Y');
        $consignment->draft = true;

        $consignment->filial_id = \Auth::user()->stafferFilialId;
    }

    public function updating(Consignment $consignment)
    {
        $this->update($consignment);

        $stock = Stock::find($consignment->stock_id);
	    $consignment->filial_id = $stock->filial_id;
    }

    public function deleting(Consignment $consignment)
    {
        $this->destroy($consignment);
    }

}
