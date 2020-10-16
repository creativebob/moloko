<?php

namespace App\Observers\System\Documents;

use App\Observers\System\BaseObserver;
use App\Models\System\Documents\Production;
use App\Stock;

class ProductionObserver extends BaseObserver
{

    /**
     * Handle the production "creating" event.
     *
     * @param Production $production
     */
    public function creating(Production $production)
    {
        $this->store($production);
        $production->date = now()->format('d.m.Y');
        $production->draft = true;

        $user = auth()->user();
	    $production->filial_id = $user->stafferFilialId;
	    $production->manufacturer_id = $user->company->we_manufacturer->id;
    }

    /**
     * Handle the production "updating" event.
     *
     * @param Production $production
     */
    public function updating(Production $production)
    {
        $this->update($production);

        $filialId = Stock::find($production->stock_id)
        ->value('filial_id');
	    $production->filial_id = $filialId;

        $amount = 0;
        $production->load('items');
        if ($production->items->isNotEmpty()) {
            $amount = $production->items->sum('amount');
        }
        $production->amount = $amount;
    }

    /**
     * Handle the production "deleting" event.
     *
     * @param Production $production
     */
    public function deleting(Production $production)
    {
        $this->destroy($production);
    }
}
