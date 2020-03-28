<?php

namespace App\Observers\System;

use App\PricesService;

use App\Observers\System\Traits\Commonable;

class PricesServiceObserver
{

    use Commonable;

    public function creating(PricesService $prices_service)
    {
        $this->store($prices_service);
        $prices_service->display = true;

        // TODO - 19.11.19 - Пока по дефолту рубль
        $prices_service->currency_id = 1;
    }

    public function created(PricesService $prices_service)
    {
        $prices_service->history()->create([
            'price' => $prices_service->price,
            'currency_id' => $prices_service->currency_id,
        ]);
    }

    public function updating(PricesService $prices_service)
    {
        $this->update($prices_service);
    }

    public function deleting(PricesService $prices_service)
    {
        $this->destroy($prices_service);
    }

}
