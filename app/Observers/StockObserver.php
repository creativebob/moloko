<?php

namespace App\Observers;

use App\Stock;

use App\Observers\Traits\Commonable;

class StockObserver
{

    use Commonable;

    public function creating(Stock $stock)
    {
        $this->store($stock);
	    $stock->filial_id = \Auth::user()->stafferFilialId;
    }

    public function updating(Stock $stock)
    {
        $this->update($stock);
    }

    public function saving(Stock $stock)
    {
        $request = request();

        $user = $request->user();
        $stock->filial_id = $user->filial_id;

        if ($request->is_production == 1) {
            Stock::where([
                'filial_id' => $request->user()->filial_id
            ])
            ->update([
                'is_production' => false
            ]);

            $stock->is_production = true;
        }

        if ($request->is_goods == 1) {
            Stock::where([
                'filial_id' => $request->user()->filial_id
            ])
                ->update([
                    'is_goods' => false
                ]);

            $stock->is_goods = true;
        }
    }

    public function deleting(Stock $stock)
    {
        $this->destroy($stock);
    }

}
