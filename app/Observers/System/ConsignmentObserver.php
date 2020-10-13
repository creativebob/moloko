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
        $consignment->receipt_date = today()->format('d.m.Y');
        $consignment->draft = true;

        $consignment->filial_id = auth()->user()->stafferFilialId;
    }

    public function updating(Consignment $consignment)
    {
        $this->update($consignment);

        $stock = Stock::find($consignment->stock_id);
	    $consignment->filial_id = $stock->filial_id;

        $amount = 0;
        $consignment->load('items');
        if ($consignment->items->isNotEmpty()) {
            $amount = $consignment->items->sum('amount');
        }
        $consignment->amount = $amount;
    }

    public function deleting(Consignment $consignment)
    {
        $this->destroy($consignment);
    }

}
