<?php

namespace App\Observers\System;

use App\Production;
use App\Observers\System\Traits\Commonable;
use App\Stock;

class ProductionObserver
{

    use Commonable;

    public function creating(Production $production)
    {
        $this->store($production);
        $production->receipt_date = now()->format('d.m.Y');
        $production->draft = true;

        $user = request()->user();
	    $production->filial_id = $user->stafferFilialId;

	    $production->manufacturer_id = $user->company->we_manufacturer->id;
    }

    public function updating(Production $production)
    {
        $this->update($production);

	    $stock = Stock::find($production->stock_id);
	    $production->filial_id = $stock->filial_id;
    }

    public function deleting(Production $production)
    {
        $this->destroy($production);
    }

}
