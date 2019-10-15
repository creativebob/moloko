<?php

namespace App\Observers;

use App\Production;
use App\Observers\Traits\Commonable;
use Carbon\Carbon;

class ProductionObserver
{

    use Commonable;

    public function creating(Production $production)
    {
        $this->store($production);
        $production->receipt_date = Carbon::now()->format('d.m.Y');
        $production->draft = true;

        $user = request()->user();
        $production->filial_id = $user->staff->first()->filial_id;
        
	    $production->manufacturer_id = $user->company->we_manufacturer->id;
    }

    public function updating(Production $production)
    {
        $this->update($production);
    }

    public function deleting(Production $production)
    {
        $this->destroy($production);
    }

}
