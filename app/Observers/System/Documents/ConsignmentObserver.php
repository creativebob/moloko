<?php

namespace App\Observers\System\Documents;

use App\Models\System\Documents\Consignment;
use App\Observers\System\BaseObserver;
use App\Stock;

class ConsignmentObserver extends BaseObserver
{

    /**
     * Handle the consignment "creating" event.
     *
     * @param Consignment $consignment
     */
    public function creating(Consignment $consignment)
    {
        $this->store($consignment);
        $consignment->date = today()->format('d.m.Y');
        $consignment->draft = true;

        $consignment->filial_id = auth()->user()->stafferFilialId;
    }

    /**
     * Handle the consignment "updating" event.
     *
     * @param Consignment $consignment
     */
    public function updating(Consignment $consignment)
    {
        $this->update($consignment);

        $filialId = Stock::find($consignment->stock_id)
        ->value('filial_id');
	    $consignment->filial_id = $filialId;

        $amount = 0;
        $consignment->load('items');
        if ($consignment->items->isNotEmpty()) {
            $amount = $consignment->items->sum('amount');
        }
        $consignment->amount = $amount;
    }

    /**
     * Handle the consignment "deleting" event.
     *
     * @param Consignment $consignment
     */
    public function deleting(Consignment $consignment)
    {
        $this->destroy($consignment);
    }
}
