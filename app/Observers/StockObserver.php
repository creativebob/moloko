<?php

namespace App\Observers;

use App\Stock;

use App\Observers\Traits\CommonTrait;

class StockObserver
{

    use CommonTrait;

    public function creating(Stock $stock)
    {
        $this->store($stock);
    }

    public function updating(Stock $stock)
    {
        $this->update($stock);
    }

    public function deleting(Stock $stock)
    {
        $this->destroy($stock);
        // dd($stock);
        // $stock->update([
        //     'editor_id' => hideGod($request->user()),
        // ]);
        // dd($stock);
    }

}
